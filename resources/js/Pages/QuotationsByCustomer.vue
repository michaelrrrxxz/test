<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../Api/Axios'
import 'vue-sonner/style.css'
import { toast } from 'vue-sonner'

import {Send} from 'lucide-vue-next'
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
} from '../components/ui/drawer'
import {Table , TableHeader, TableRow, TableHead, TableBody} from '../components/ui/table'
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../components/ui/card'
import { Button } from '../components/ui/button'
import { Input } from '../components/ui/input'
import { Label } from '../components/ui/label'
import { useRoute } from 'vue-router'
import AppLayout from '../layouts/AppLayout.vue'
import AddQuotation from '../components/Forms/AddQuotation.vue'
import EditQuotation from '@/components/Forms/EditQuotation.vue'

const route = useRoute()

interface QuotationItem {
  product_name: string
  item_description: string
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
const customerEmail = computed(() => customer.value?.email ?? '');
const quotations = ref<Quotation[]>([])
const customer = ref<Customer | null>(null)
const loading = ref(false)

console.log('Customer Email' + customerEmail.value)

// search
const searchQuery = ref('')

//drawer
const isAddOpen = ref(false)
const isEditOpen = ref(false)

//form state
const form = reactive({
  id: null as number | null,
  quotation_date: '',
  items: [] as QuotationItem[],
  errors: {} as Record<string, any>,
  processing: false,
})

// computed totals
const totalItems = computed(() =>
  form.items.reduce((sum, i) => sum + (Number(i.quantity) || 0), 0)
)

const grandTotal = computed(() =>
  form.items.reduce(
    (sum, i) =>
      sum + (Number(i.quantity) || 0) * (Number(i.unit_cost) || 0),
    0
  )
)

// filtered quotations
const filteredQuotations = computed(() => {
  if (!searchQuery.value.trim()) return quotations.value
  const query = searchQuery.value.toLowerCase()
  return quotations.value.filter(q =>
    q.id.toString().includes(query) ||
    q.quotation_date.toLowerCase().includes(query) ||
    q.items.some(item => item.product_name.toLowerCase().includes(query))
  )
})

// fetch
async function fetchCustomerAndQuotations() {
  loading.value = true
  try {
    const [customerRes, quotationsRes] = await Promise.all([
      api.get(`/customers/${customerId.value}`),
      api.get(`/customers/${customerId.value}/quotations`)
    ])
    customer.value = customerRes.data.data
    quotations.value = quotationsRes.data
  } catch {
    toast.error('Failed to load customer or quotations')
  } finally {
    loading.value = false
  }
}

onMounted(fetchCustomerAndQuotations)



function addItem() {
  form.items.push({ product_name: '', item_description: '', quantity: 1, unit_cost: 0 })
    toast.success('New item added')
}

function removeItem(index: number) {
  if (form.items.length > 1) form.items.splice(index, 1)
     toast.success('Item removed')
}



function openEditDrawer(quotation: Quotation) {
  form.id = quotation.id
  form.quotation_date = quotation.quotation_date
  form.items = quotation.items.map(i => ({
    product_name: i.product_name,
    item_description: i.item_description,
    quantity: i.quantity,
    unit_cost: i.unit_cost
  }))
  form.errors = {}
  isEditOpen.value = true
}

// validation
function validateForm() {
  form.errors = {}
  if (!form.quotation_date) {
    form.errors.quotation_date = 'Quotation date is required.'
  }
  form.items.forEach((item, i) => {
    if (!item.product_name.trim()) form.errors[`items.${i}.product_name`] = 'Product name required.'
    if (item.quantity < 1) form.errors[`items.${i}.quantity`] = 'Quantity must be at least 1.'
    if (item.unit_cost < 0) form.errors[`items.${i}.unit_cost`] = 'Unit cost must be >= 0.'
  })
  return Object.keys(form.errors).length === 0
}

// edit
async function submitEdit() {
  if (!validateForm()) return
  form.processing = true
  try {
    await api.put(`/quotations/${form.id}`, {
      quotation_date: form.quotation_date,
      total_items: totalItems.value,
      grand_total: grandTotal.value,
      items: form.items.map(i => ({
        product_name: i.product_name,
        item_description: i.item_description,
        quantity: i.quantity,
        price: i.unit_cost,
      })),
    })
    toast.success('Quotation updated')
    isEditOpen.value = false
    form.items = [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }]
    await fetchCustomerAndQuotations()
  } catch (error: any) {
    if (error.response?.data?.errors) {
      form.errors = error.response.data.errors
    } else {
      toast.error('Failed to update quotation')
    }
  } finally {
    form.processing = false
  }
}

// delete
function deleteQuotation(id: number | string) {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the quotation.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`/quotations/${id}`)
          toast.success('Quotation deleted')
          await fetchCustomerAndQuotations()
        } catch {
          toast.error('Failed to delete quotation')
        }
      },
    },
  })
}

// send email
const sendingEmail = ref<number | null>(null)
function sendQuotationEmail(quotationId: number) {
  toast("Are you sure?", {
    description: `Send quotation to ${customerEmail.value}.`,
    position: 'top-center',

    action: {
      label: "Send",
      onClick: async () => {
        sendingEmail.value = quotationId;
        try {
          await api.post(`/quotations/${quotationId}/send-email`);
          toast.success("Quotation sent to customer");
        } catch {

          toast.error("Failed to send email"

          );
        } finally {
          sendingEmail.value = null;
        }
      }
    }
  });
}
</script>


<template>
<AppLayout :customerId="customerId">
  <div class="max-w-4xl mx-auto p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
    <h2
        class="text-2xl font-semibold"
        @click="$router.push('/customers')"
        >
        Quotations of {{ customer?.name }}
    </h2>
        <Button @click="isAddOpen = true">+ Add Quotation</Button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <Input placeholder="Search quotations..." v-model="searchQuery" />
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="n in 3" :key="n" class="animate-pulse bg-gray-200 h-24 rounded"></div>
    </div>

    <!-- Customer Info -->
     <div v-else class="mb-6">
      <Card>
        <CardHeader>
          <CardTitle>Customer Information</CardTitle>
        </CardHeader>
        <CardContent class="space-y-2">
          <p><span class="font-semibold">Name:</span> {{ customer?.name || 'N/A' }}</p>
          <p><span class="font-semibold">Email:</span> {{ customer?.email || 'N/A' }}</p>
          <p><span class="font-semibold">Address:</span> {{ customer?.address || 'N/A' }}</p>
          <p><span class="font-semibold">Contact Number:</span> {{ customer?.contact_number || 'N/A' }}</p>
        </CardContent>
      </Card>
    </div>

    <!-- No quotations -->
    <div v-if="!loading && filteredQuotations.length === 0" class="text-gray-500 text-center py-8">
      No quotations found.
    </div>

    <!-- Quotations List -->
    <div v-else>
      <div v-for="quotation in filteredQuotations" :key="quotation.id" class="mb-8 p-4 border rounded bg-white shadow-sm">
        <div class="flex justify-between items-center mb-2">
          <div>
          <!-- <div
          v-for="(quotation, index) in quotations"
          :key="quotation.id"
          class="font-semibold text-lg"
        >
          Quotation #{{ index + 1 }}
        </div> -->

            <div class="text-sm text-gray-500">Date: {{ quotation.quotation_date }}</div>
          </div>
          <div class="flex gap-2">
            <Button size="sm" variant="outline" @click="openEditDrawer(quotation)">Edit</Button>
            <Button size="sm" variant="destructive" @click="deleteQuotation(quotation.id)">Delete</Button>
            <Button size="sm" :disabled="sendingEmail === quotation.id" @click="sendQuotationEmail(quotation.id)">
              <span v-if="sendingEmail === quotation.id" class="animate-spin mr-2 h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
              <Send />
              Send via Email
            </Button>
          </div>
        </div>

        <div class="mb-2">
          <span class="font-semibold">Grand Total:</span>
          ₱{{ quotation.grand_total.toFixed(2) }} |
          Total Items: {{ quotation.total_items }}
        </div>

        <div class="mb-2">
          <span class="font-semibold">Items:</span>
          <Table class="w-full table-auto border-collapse border border-gray-300 mt-2">

            <TableHeader>
                <TableHead class="w-[20%]">Product</TableHead>
                <TableHead class="w-[20%]">Description</TableHead>
                <TableHead class="w-[20%]">Quantity</TableHead>
                <TableHead class="w-[20%]">Unit Cost</TableHead>
                <TableHead class="w-[20%]">Total</TableHead>
            </TableHeader>
            <tbody>
              <tr v-for="(item, idx) in quotation.items" :key="idx">
                <td class="border px-2 py-1">{{ item.product_name }}</td>
                <td class="border px-y py-1">{{ item.item_description }}</td>
                <td class="border px-2 py-1">{{ item.quantity }}</td>
                <td class="border px-2 py-1">₱{{ item.unit_cost.toFixed(2) }}</td>
                <td class="border px-2 py-1">₱{{ ((Number(item.quantity) || 0) * (Number(item.unit_cost) || 0)).toFixed(2) }}</td>
              </tr>
            </tbody>
          </Table>
        </div>
      </div>
    </div>

    <!-- Add Quotation Drawer -->
    <AddQuotation
    v-model="isAddOpen"
    :customer-id="customerId"
    @saved="fetchCustomerAndQuotations"
    />
<!--
    <EditQuotation
    v-model="isEditOpen"
    :form="form"
    /> -->
    <!-- Edit Quotation Drawer -->
     <Drawer v-model:open="isEditOpen">
        <DrawerContent class="flex flex-col max-h-screen">
            <!-- Header -->
            <DrawerHeader class="shrink-0">
            <DrawerTitle>Edit Quotation</DrawerTitle>
            <DrawerDescription>Update the details below.</DrawerDescription>
            </DrawerHeader>

            <!-- Scrollable Form Body -->
            <form
            id="editQuotationForm"
            class="flex-1 overflow-y-auto p-4 space-y-4"
            @submit.prevent="submitEdit"
            >
            <!-- Date -->
            <div>
                <Label for="edit_quotation_date">Quotation Date</Label>
                <Input
                id="edit_quotation_date"
                type="date"
                v-model="form.quotation_date"
                :disabled="form.processing"
                required
                />
                <p v-if="form.errors.quotation_date" class="text-sm text-red-600 mt-1">
                {{ form.errors.quotation_date }}
                </p>
            </div>

            <!-- Items -->
            <div>
                <Label>Items</Label>
                <div
                v-for="(item, index) in form.items"
                :key="index"
                class="grid grid-cols-12 gap-2 items-center mb-3"
                >
                <!-- Product Name -->
                <div class="col-span-3">
                    <Label :for="`edit_product_name_${index}`">Product Name</Label>
                    <Input
                    :id="`edit_product_name_${index}`"
                    placeholder="Product Name"
                    v-model="item.product_name"
                    :disabled="form.processing"
                    required
                    />
                    <p
                    v-if="form.errors[`items.${index}.product_name`]"
                    class="text-sm text-red-600 mt-1"
                    >
                    {{ form.errors[`items.${index}.product_name`] }}
                    </p>
                </div>

                <!-- Description -->
                <div class="col-span-4">
                    <Label :for="`edit_item_description_${index}`">Description (optional)</Label>
                    <Input
                    :id="`edit_item_description_${index}`"
                    placeholder="Description"
                    v-model="item.item_description"
                    :disabled="form.processing"
                    />
                    <p
                    v-if="form.errors[`items.${index}.item_description`]"
                    class="text-sm text-red-600 mt-1"
                    >
                    {{ form.errors[`items.${index}.item_description`] }}
                    </p>
                </div>

                <!-- Quantity -->
                <div class="col-span-2">
                    <Label :for="`edit_quantity_${index}`">Quantity</Label>
                    <Input
                    :id="`edit_quantity_${index}`"
                    type="number"
                    min="1"
                    v-model="item.quantity"
                    :disabled="form.processing"
                    required
                    />
                    <p
                    v-if="form.errors[`items.${index}.quantity`]"
                    class="text-sm text-red-600 mt-1"
                    >
                    {{ form.errors[`items.${index}.quantity`] }}
                    </p>
                </div>

                <!-- Unit Cost -->
                <div class="col-span-2">
                    <Label :for="`edit_unit_cost_${index}`">Unit Cost</Label>
                    <Input
                    :id="`edit_unit_cost_${index}`"
                    type="number"
                    min="0"
                    step="0.01"
                    v-model="item.unit_cost"
                    :disabled="form.processing"
                    required
                    />
                    <p
                    v-if="form.errors[`items.${index}.unit_cost`]"
                    class="text-sm text-red-600 mt-1"
                    >
                    {{ form.errors[`items.${index}.unit_cost`] }}
                    </p>
                </div>

                <!-- Total -->
                    <div class="col-span-1
                                flex items-center justify-center">
                                <span class="font-semibold">
                                    ₱{{ (item.quantity * item.unit_cost).toFixed(2) }}
                                </span>
                                </div>

                <!-- Remove -->
                <div class="col-span-1">
                    <Button
                    variant="destructive"
                    size="sm"
                    type="button"
                    @click="removeItem(index)"
                    :disabled="form.processing || form.items.length === 1"
                    >
                    Remove
                    </Button>
                </div>
                </div>
            </div>
            </form>

<!-- Footer section -->
<div class="flex flex-col shrink-0 p-4 border-t border-gray-300 space-y-4">
  <!-- Totals row -->
  <div class="flex items-center justify-between font-semibold">
    <!-- Left: Add Item -->
    <Button type="button" size="sm" @click="addItem">
      + Add Item
    </Button>

    <!-- Right: Totals -->
    <div class="flex items-center space-x-6">
      <span>Total Items: {{ totalItems }}</span>
      <span>₱{{ (Number(grandTotal) || 0).toFixed(2) }}</span>
    </div>
  </div>

  <!-- Action buttons -->
  <div class="flex justify-end space-x-2">
    <Button variant="outline" @click="isEditOpen = false" :disabled="form.processing">
      Cancel
    </Button>
    <Button type="submit" form="editQuotationForm" :disabled="form.processing">
      Update Quotation
    </Button>
  </div>
</div>

        </DrawerContent>
    </Drawer>

  </div>
</AppLayout>
</template>
