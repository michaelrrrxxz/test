<template>
  <Drawer v-model:open="isOpen">
    <DrawerContent class="flex flex-col max-h-screen">
      <DrawerHeader>
        <DrawerTitle>Add Quotation</DrawerTitle>
        <DrawerDescription>Fill in the quotation details</DrawerDescription>
      </DrawerHeader>

      <form id="addQuotationForm" class="flex-1 overflow-y-auto p-4 space-y-4" @submit.prevent="submitAdd">
        <div>
          <Label for="quotation_date">Quotation Date</Label>
          <Input
            id="quotation_date"
            type="date"
            range="today"
            v-model="form.quotation_date"
            :disabled="form.processing"
          />
          <p v-if="form.errors.quotation_date" class="text-red-600 text-sm">
            {{ form.errors.quotation_date }}
          </p>
        </div>

        <!-- Items list -->
        <div>
        <Label>Items</Label>

        <div
            v-for="(item, index) in form.items"
            :key="index"
            class="grid grid-cols-12 gap-2 items-center mb-3"
        >
            <!-- Product Name -->
            <div class="col-span-3">
            <Label>Product Name</Label>
            <Input v-model="item.product_name" placeholder="Product" />
            <p v-if="form.errors[`items.${index}.product_name`]" class="text-red-500 text-sm">
                {{ form.errors[`items.${index}.product_name`] }}
            </p>
            </div>

            <!-- Description -->
            <div class="col-span-4">
            <Label>Description</Label>
            <Input v-model="item.item_description" placeholder="Description" />
            </div>

            <!-- Quantity -->
            <div class="col-span-2">
            <Label>Quantity</Label>
            <Input v-model.number="item.quantity" type="number" min="1" />
            <p v-if="form.errors[`items.${index}.quantity`]" class="text-red-500 text-sm">
                {{ form.errors[`items.${index}.quantity`] }}
            </p>
            </div>

            <!-- Unit Cost -->
            <div class="col-span-2">
            <Label>Unit Cost</Label>
            <Input v-model.number="item.unit_cost" type="number" step="0.01" />
            <p v-if="form.errors[`items.${index}.unit_cost`]" class="text-red-500 text-sm">
                {{ form.errors[`items.${index}.unit_cost`] }}
            </p>
            </div>
            <!-- Total Cost -->
            <div class="col-span-1
            flex items-center justify-center">
            <span class="font-semibold">
                ₱{{ (item.quantity * item.unit_cost).toFixed(2) }}
            </span>
            </div>
            <!-- Remove Button -->
            <div class="col-span-1 flex justify-center">
            <Button type="button" @click="removeItem(index)" variant="destructive">
                Remove
            </Button>
            </div>
        </div>

        <!-- Add Item Button -->

        </div>

        <!-- Totals -->


      </form>
    <div class="flex flex-col shrink-0 p-4 border-t space-y-4">
    <!-- Totals row -->
    <div class="flex items-center justify-between font-semibold">
        <!-- Left: Add Item -->
        <Button type="button" size="sm" @click="addItem">
        + Add Item
        </Button>

        <!-- Right: Totals -->
            <div class="flex items-center space-x-6">
            <span>Total Items: {{ totalItems }}</span>
            <span>₱{{ grandTotal.toFixed(2) }}</span>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex justify-end space-x-2">
            <Button variant="outline" @click="resetForm">Cancel</Button>
            <Button type="submit" form="addQuotationForm" :disabled="form.processing">Save</Button>
        </div>
    </div>
    </DrawerContent>
  </Drawer>
</template>

<script setup lang="ts">
import { reactive, computed } from 'vue'
import api from '../../Api/Axios'
import { toast } from 'vue-sonner'
import { Drawer, DrawerContent, DrawerHeader, DrawerTitle, DrawerDescription } from '../ui/drawer'
import { Button } from '../ui/button'
import { Input } from '../ui/input'
import { Label } from '../ui/label'

const props = defineProps<{
  customerId: string
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'saved'): void
}>()

const isOpen = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const form = reactive({
  quotation_date: '',
  items: [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }],
  errors: {} as Record<string, any>,
  processing: false

})
function resetForm() {
  form.quotation_date = '';
  form.items = [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }];
  form.errors = {};
  isOpen.value = false;
  form.processing = false;
}

const totalItems = computed(() => form.items.length)
const grandTotal = computed(() =>
  form.items.reduce((sum, i) => sum + (Number(i.quantity) || 0) * (Number(i.unit_cost) || 0), 0)
)

function addItem() {
  form.items.push({ product_name: '', item_description: '', quantity: 1, unit_cost: 0 })
   toast.success('New item added')
}

function removeItem(index: number) {
  if (form.items.length > 1) form.items.splice(index, 1)
    toast.success('Item removed')
}

function validateForm() {
  form.errors = {}
  if (!form.quotation_date) form.errors.quotation_date = 'Quotation date is required.'
  return Object.keys(form.errors).length === 0
}

async function submitAdd() {
  if (!validateForm()) return
  form.processing = true
  try {
    await api.post(`/customers/${props.customerId}/quotations`, {
      quotation_date: form.quotation_date,
      customer_id: props.customerId,
      total_items: totalItems.value,
      grand_total: grandTotal.value,
      items: form.items.map(i => ({
        product_name: i.product_name,
        item_description: i.item_description,
        quantity: i.quantity,
        price: i.unit_cost,
      }))
    })
    toast.success('Quotation added')
    emit('saved')
    isOpen.value = false
    form.quotation_date = ''
    form.items = [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }]
  } catch (error: any) {
    if (error.response?.data?.errors) {
      form.errors = error.response.data.errors
    } else {
      toast.error('Failed to add quotation')
    }
  } finally {
    form.processing = false
  }
}
</script>
