<template>
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
            :max="today"
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
                v-model.number="item.quantity"
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
                v-model.number="item.unit_cost"
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
            <div class="col-span-1 flex items-center justify-center">
              <span class="font-semibold">
                ₱{{ (Number(item.quantity) * Number(item.unit_cost)).toFixed(2) }}
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
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import api from '@/Api/Axios'
import { toast } from 'vue-sonner'
import 'vue-sonner/style.css'

// UI imports
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription
} from '@/components/ui/drawer'

interface QuotationItem {
  product_name: string
  item_description?: string
  quantity: number
  unit_cost: number
}

interface QuotationForm {
  quotation_date: string
  items: QuotationItem[]
  errors: Record<string, string>
  processing: boolean
}

const props = defineProps<{
  isEditOpen: boolean
  quotationId: number | string | null
  quotationData?: {
    quotation_date: string
    items: QuotationItem[]
  }
}>()

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
  (e: 'updated'): void
}>()

const isEditOpen = ref(props.isEditOpen)
watch(() => props.isEditOpen, (val) => {
  isEditOpen.value = val
})
watch(isEditOpen, (val) => {
  emit('update:open', val)
})


const today = new Date().toISOString().split('T')[0]

const form = reactive<QuotationForm>({
  quotation_date: today,
  items: [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }],
  errors: {},
  processing: false
})

watch(() => props.quotationData, (data) => {
  if (data) {
    form.quotation_date = data.quotation_date || today
    form.items = data.items.length
      ? JSON.parse(JSON.stringify(data.items))
      : [{ product_name: '', item_description: '', quantity: 1, unit_cost: 0 }]
    form.errors = {}
    form.processing = false
  }
}, { immediate: true })

const totalItems = computed(() =>
  form.items.reduce((sum, item) => sum + Number(item.quantity || 0), 0)
)

const grandTotal = computed(() =>
  form.items.reduce(
    (sum, item) => sum + (Number(item.quantity) || 0) * (Number(item.unit_cost) || 0),
    0
  )
)

function addItem() {
  form.items.push({ product_name: '', item_description: '', quantity: 1, unit_cost: 0 })
}

function removeItem(index: number) {
  if (form.items.length > 1) {
    form.items.splice(index, 1)
  }
}

async function submitEdit() {
  if (!props.quotationId) return
  form.processing = true
  form.errors = {}

  try {
    await api.put(`/quotations/${props.quotationId}`, {
      quotation_date: form.quotation_date,
      items: form.items
    })
    toast.success('Quotation updated successfully')
    isEditOpen.value = false
    emit('updated')
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
</script>

