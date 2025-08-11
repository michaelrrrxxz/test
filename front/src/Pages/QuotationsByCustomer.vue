<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/Api/Axios'
import { toast } from 'vue-sonner'
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
} from '@/components/ui/drawer'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useRoute } from 'vue-router'
const route = useRoute()

interface QuotationItem {
  product_name: string
  quantity: number
  unit_cost: number
}

interface Quotation {
  id: number
  quotation_date: string
  grand_total: number
  total_items: number
  items: QuotationItem[]
}

interface Customer {
  id: number
  name: string
  date_of_birth: string
  address: string
  email: string
  contact_number: string
}




const customerId = ref(route.params.customerId as string)
const quotations = ref<Quotation[]>([])
const customer = ref<Customer | null>(null)
const loading = ref(false)

// Fetch customer info and quotations
async function fetchCustomerAndQuotations() {
  loading.value = true
  try {
    const [customerRes, quotationsRes] = await Promise.all([
      api.get(`/customers/${customerId.value}`),
      api.get(`/customers/${customerId.value}/quotations`)
    ])
    customer.value = customerRes.data
    quotations.value = quotationsRes.data
  } catch {
    toast.error('Failed to load customer or quotations')
  } finally {
    loading.value = false
  }
}

onMounted(fetchCustomerAndQuotations)

// Add Quotation drawer state
const isAddOpen = ref(false)

// Form reactive state
const addForm = reactive({
  quotation_date: '',
  items: [] as QuotationItem[],
  errors: {} as Record<string, any>,
  processing: false,
})

// Computed total items and grand total
const totalItems = computed(() => addForm.items.reduce((sum, i) => sum + i.quantity, 0))
const grandTotal = computed(() =>
  addForm.items.reduce((sum, i) => sum + i.quantity * i.unit_cost, 0)
)

// Initialize form with one empty item
function initForm() {
  addForm.quotation_date = ''
  addForm.items = [{ product_name: '', quantity: 1, unit_cost: 0 }]
  addForm.errors = {}
}
initForm()

function addItem() {
  addForm.items.push({ product_name: '', quantity: 1, unit_cost: 0 })
}

function removeItem(index: number) {
  if (addForm.items.length > 1) addForm.items.splice(index, 1)
}

// Submit new quotation
async function submitAddQuotation() {
  addForm.processing = true
  addForm.errors = {}

  // Simple validation
  if (!addForm.quotation_date) {
    addForm.errors.quotation_date = 'Quotation date is required.'
  }
  addForm.items.forEach((item, i) => {
    if (!item.product_name.trim()) addForm.errors[`items.${i}.product_name`] = 'Product name required.'
    if (item.quantity < 1) addForm.errors[`items.${i}.quantity`] = 'Quantity must be at least 1.'
    if (item.unit_cost < 0) addForm.errors[`items.${i}.unit_cost`] = 'Unit_cost must be >= 0.'
  })

  if (Object.keys(addForm.errors).length > 0) {
    addForm.processing = false
    return
  }

  try {
    await api.post(`/customers/${customerId.value}/quotations`, {
      quotation_date: addForm.quotation_date,
      customer_id: customerId.value,
      total_items: totalItems.value,
      grand_total: grandTotal.value,
      items: addForm.items.map(i => ({
        product_name: i.product_name,
        quantity: i.quantity,
        price: i.unit_cost,
      })),
    })
    toast.success('Quotation added')
    isAddOpen.value = false
    await fetchCustomerAndQuotations()
    initForm()
  } catch (error: any) {
    if (error.response?.data?.errors) {
      addForm.errors = error.response.data.errors
    } else {
      toast.error('Failed to add quotation')
    }
  } finally {
    addForm.processing = false
  }
}

// Send quotation via email
const sendingEmail = ref<number | null>(null)
async function sendQuotationEmail(quotationId: number) {
  sendingEmail.value = quotationId
  try {
    await api.post(`/quotations/${quotationId}/send-email`)
    toast.success('Quotation sent to customer')
  } catch {
    toast.error('Failed to send email')
  } finally {
    sendingEmail.value = null
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const res = await api.get(`/customers/${customerId.value}`)
    customer.value = res.data.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="max-w-4xl mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold">Quotations for Customer #{{ customerId }}</h2>
      <Button size="sm" @click="isAddOpen = true">Add Quotation</Button>
    </div>
<div v-if="loading">Loading...</div>
<div v-else class="mb-6 p-4 bg-gray-50 rounded border">
  <div class="font-bold text-lg mb-1">Customer Information</div>
  <div><span class="font-semibold">Name:</span> {{ customer?.name || 'N/A' }}</div>
  <div><span class="font-semibold">Email:</span> {{ customer?.email || 'N/A' }}</div>
</div>



    <div v-if="loading" class="text-center py-6">Loading...</div>

    <div v-else>
      <div v-if="quotations.length === 0" class="text-gray-500 text-center py-8">
        No quotations found for this customer.
      </div>
      <div v-else>
        <div v-for="quotation in quotations" :key="quotation.id" class="mb-8 p-4 border rounded bg-white shadow-sm">
          <div class="flex justify-between items-center mb-2">
            <div>
              <div class="font-semibold text-lg">Quotation #{{ quotation.id }}</div>
              <div class="text-sm text-gray-500">Date: {{ quotation.quotation_date }}</div>
            </div>
            <Button size="sm" :loading="sendingEmail === quotation.id" @click="sendQuotationEmail(quotation.id)"
              :disabled="sendingEmail === quotation.id">
              Send to customer via email
            </Button>
          </div>
          <div class="mb-2">
            <span class="font-semibold">Grand Total:</span>
            ₱{{ quotation.grand_total.toFixed(2) }}
            &nbsp; | &nbsp;
            <span class="font-semibold">Total Items:</span>
            {{ quotation.total_items }}
          </div>
          <div class="mb-2">
            <span class="font-semibold">Items:</span>
            <table class="w-full table-auto border-collapse border border-gray-300 mt-2">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border px-2 py-1">Product Name</th>
                  <th class="border px-2 py-1">Quantity</th>
                  <th class="border px-2 py-1">Unit_cost</th>
                  <th class="border px-2 py-1">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, idx) in quotation.items" :key="idx">
                  <td class="border px-2 py-1">{{ item.product_name }}</td>
                  <td class="border px-2 py-1">{{ item.quantity }}</td>
                  <td class="border px-2 py-1">₱{{ item.unit_cost.toFixed(2) }}</td>
                  <td class="border px-2 py-1">₱{{ (item.quantity * item.unit_cost).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Quotation Drawer -->
    <Drawer v-model:open="isAddOpen">
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>Add Quotation</DrawerTitle>
          <DrawerDescription>Fill in the details below.</DrawerDescription>
        </DrawerHeader>

        <form class="p-4 space-y-4" @submit.prevent="submitAddQuotation">
          <!-- Quotation Date -->
          <div>
            <Label for="quotation_date">Quotation Date</Label>
            <Input id="quotation_date" type="date" v-model="addForm.quotation_date" :disabled="addForm.processing"
              required />
            <p v-if="addForm.errors.quotation_date" class="text-sm text-red-600 mt-1">
              {{ addForm.errors.quotation_date }}
            </p>
          </div>

          <!-- Items -->
          <div>
            <Label>Items</Label>
            <div v-for="(item, index) in addForm.items" :key="index" class="grid grid-cols-12 gap-2 items-center mb-3">
              <div class="col-span-5">
                <Label :for="`product_name_${index}`">Product Name</Label>
                <Input :id="`product_name_${index}`" placeholder="Product Name" v-model="item.product_name"
                  :disabled="addForm.processing" required />
                <p v-if="addForm.errors[`items.${index}.product_name`]" class="text-sm text-red-600 mt-1">
                  {{ addForm.errors[`items.${index}.product_name`] }}
                </p>
              </div>
              <div class="col-span-2">
                <Label :for="`quantity_${index}`">Quantity</Label>
                <Input :id="`quantity_${index}`" type="number" min="1" v-model.number="item.quantity"
                  :disabled="addForm.processing" required />
                <p v-if="addForm.errors[`items.${index}.quantity`]" class="text-sm text-red-600 mt-1">
                  {{ addForm.errors[`items.${index}.quantity`] }}
                </p>
              </div>
              <div class="col-span-3">
                <Label :for="`unit_cost_${index}`">Unit_cost</Label>
                <Input :id="`unit_cost_${index}`" type="number" min="0" step="0.01" v-model.number="item.unit_cost"
                  :disabled="addForm.processing" required />
                <p v-if="addForm.errors[`items.${index}.unit_cost`]" class="text-sm text-red-600 mt-1">
                  {{ addForm.errors[`items.${index}.unit_cost`] }}
                </p>
              </div>
              <div class="col-span-1 text-center font-mono">${{ (item.quantity * item.unit_cost).toFixed(2) }}</div>
              <div class="col-span-1">
                <Button variant="destructive" size="sm" type="button" @click="removeItem(index)"
                  :disabled="addForm.processing || addForm.items.length === 1">
                  Remove
                </Button>
              </div>
            </div>

            <Button type="button" size="sm" @click="addItem" :disabled="addForm.processing">
              + Add Item
            </Button>
          </div>

          <div class="flex justify-between font-semibold pt-4 border-t border-gray-300">
            <div>Total Items: {{ totalItems }}</div>
            <div>Grand Total: ${{ grandTotal.toFixed(2) }}</div>
          </div>

          <div class="flex justify-end space-x-2 pt-4 border-t border-gray-300">
            <Button variant="outline" @click="isAddOpen = false" :disabled="addForm.processing">Cancel</Button>
            <Button type="submit" :disabled="addForm.processing">Save Quotation</Button>
          </div>
        </form>
      </DrawerContent>
    </Drawer>
  </div>
</template>
