<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../Api/Axios'
import { toast } from 'vue-sonner'
import { useRouter } from 'vue-router'
import 'vue-sonner/style.css'

import AppLayout from '@/layouts/AppLayout.vue'


// import {
//   Dialog,
//   DialogContent,
//   DialogHeader,
//   DialogTitle,
//   DialogDescription,
// } from '@/components/ui/dialog'

import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
  DrawerFooter,
  DrawerClose,
} from '../components/ui/drawer'

import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../components/ui/table'
import { Button } from '../components/ui/button'
import { Input } from '../components/ui/input'
import { Label } from '../components/ui/label'



interface Customer {
  id: number | string
  name: string
  email: string
  phone: string
  date_of_birth: string
  address: string
  contact_number: string
}

const router = useRouter()

const customers = ref<Customer[]>([])
const searchQuery = ref('')
const loading = ref(true)

const isAddOpen = ref(false)
const isEditOpen = ref(false)
const editId = ref<number | string | null>(null)

const addForm = reactive({
  name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: '',
  contact_number: '',
  errors: {} as Record<string, string>,
  processing: false,
})

const editForm = reactive({
  name: '',
  email: '',
  phone: '',
  date_of_birth: '',
  address: '',
  contact_number: '',
  errors: {} as Record<string, string[]>,
  processing: false,
})

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  return customers.value.filter(c =>
    c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    c.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

async function fetchCustomers() {
  loading.value = true
  try {
    const response = await api.get('/customers')
    customers.value = response.data.data // API wraps in .data
  } catch {
    toast.error('Failed to load customers')
  } finally {
    loading.value = false
  }
}

onMounted(fetchCustomers)

function openAdd() {
  Object.assign(addForm, {
    name: '', email: '', phone: '', date_of_birth: '', address: '', contact_number: '', errors: {}, processing: false
  })
  isAddOpen.value = true
}

function openEdit(customer: Customer) {
  editId.value = customer.id
  Object.assign(editForm, { ...customer, errors: {}, processing: false })
  isEditOpen.value = true
}

function openViewQuotations(customer: Customer) {
  router.push(`/quotations/${customer.id}`)
}

async function submitAdd() {
  addForm.processing = true
  addForm.errors = {}
  try {
    await api.post('/customers', { ...addForm })
    toast.success('Customer created')
    isAddOpen.value = false
    await fetchCustomers()
  } catch (error: any) {
    if (error.response?.data?.errors) {
      addForm.errors = error.response.data.errors
    } else {
      toast.error('Failed to create customer')
    }
  } finally {
    addForm.processing = false
  }
}


async function submitEdit() {
  if (!editId.value) return
  editForm.processing = true
  editForm.errors = {}
  try {
    await api.put(`/customers/${editId.value}`, { ...editForm })
    toast.success('Customer updated')
    isEditOpen.value = false
    editId.value = null
    await fetchCustomers()
  } catch (error: any) {
    if (error.response?.data?.errors) editForm.errors = error.response.data.errors
    else toast.error('Failed to update customer')
  } finally {
    editForm.processing = false
  }
}

function deleteCustomer(id: number | string) {
  toast('Are you sure?', {
    position: 'top-center',
    description: 'This will permanently delete the customer.',
    action: {
      label: 'Confirm',
      onClick: async () => {
        try {
          await api.delete(`/customers/${id}`)
          toast.success('Customer deleted')
          await fetchCustomers()
        } catch {
          toast.error('Failed to delete customer')
        }
      },
    },
  })
}
</script>
<template>
<AppLayout>
  <div class="p-2 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold">Customers</h2>
      <div class="flex gap-2">
        <input v-model="searchQuery" type="text" placeholder="Search by name or email"
               class="border rounded px-2 py-1 text-sm" />
        <Button size="sm" variant="secondary" @click="openAdd">Add Customer</Button>
      </div>
    </div>

<Table class="w-full table-fixed">
  <TableHeader>
    <TableRow>
      <TableHead class="w-[20%]">Name</TableHead>
      <TableHead class="w-[25%]">Email</TableHead>
      <TableHead class="w-[25%]">Address</TableHead>
      <TableHead class="w-[15%]">Contact</TableHead>
      <TableHead class="w-[15%]">Actions</TableHead>
    </TableRow>
  </TableHeader>

  <!-- Skeleton loader -->
  <TableBody v-if="loading">
    <TableRow v-for="n in 5" :key="n">
      <TableCell colspan="6" class="animate-pulse py-3">
        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
      </TableCell>
    </TableRow>
  </TableBody>

  <TableBody v-else-if="filteredCustomers.length">
    <TableRow
      v-for="customer in filteredCustomers"
      :key="customer.id"
      class="hover:bg-gray-50"
    >
      <TableCell class="truncate">{{ customer.name }}</TableCell>
      <TableCell class="truncate">{{ customer.email }}</TableCell>
      <TableCell class="truncate">{{ customer.address }}</TableCell>
      <TableCell class="truncate">{{ customer.contact_number }}</TableCell>
      <TableCell class="space-x-2">
        <Button size="sm" variant="secondary" @click="openViewQuotations(customer)">View Quotations</Button>
        <Button size="sm" variant="outline" @click="openEdit(customer)">Edit</Button>
        <Button size="sm" variant="destructive" @click="deleteCustomer(customer.id)">Delete</Button>
      </TableCell>
    </TableRow>
  </TableBody>

  <TableBody v-else>
    <TableRow>
      <TableCell :colSpan="6" class="text-center py-6 text-gray-500">
        No customers found.
      </TableCell>
    </TableRow>
  </TableBody>
</Table>


   <Drawer v-model:open="isAddOpen">
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>Add Customer</DrawerTitle>
          <DrawerDescription>Fill in the details below.</DrawerDescription>
        </DrawerHeader>

 <form class="p-4 space-y-4" @submit.prevent="submitAdd">
    <!-- Name -->
    <div>
      <Label for="add-name">Name</Label>
      <Input
        id="add-name"
        v-model="addForm.name"
        placeholder="Name"
        :disabled="addForm.processing"
        required
        :class="addForm.errors.name ? 'border-red-500 focus:border-red-500' : ''"
      />
      <p v-if="addForm.errors.name" class="text-sm text-red-600">
        {{ addForm.errors.name[0] }}
      </p>
    </div>

    <!-- Email -->
    <div>
      <Label for="add-email">Email</Label>
      <Input
        id="add-email"
        v-model="addForm.email"
        type="email"
        placeholder="Email"
        :disabled="addForm.processing"
        required
        :class="addForm.errors.email ? 'border-red-500 focus:border-red-500' : ''"
      />
      <p v-if="addForm.errors.email" class="text-sm text-red-600">
        {{ addForm.errors.email[0] }}
      </p>
    </div>

    <!-- Date of Birth -->
    <div>
      <Label for="add-dob">Date of Birth</Label>
      <Input
        id="add-dob"
        v-model="addForm.date_of_birth"
        type="date"
        placeholder="YYYY-MM-DD"
        :disabled="addForm.processing"
        required
        :class="addForm.errors.date_of_birth ? 'border-red-500 focus:border-red-500' : ''"
      />
      <p v-if="addForm.errors.date_of_birth" class="text-sm text-red-600">
        {{ addForm.errors.date_of_birth[0] }}
      </p>
    </div>

    <!-- Address -->
    <div>
      <Label for="add-address">Address</Label>
      <Input
        id="add-address"
        v-model="addForm.address"
        placeholder="Address"
        :disabled="addForm.processing"
        required
        :class="addForm.errors.address ? 'border-red-500 focus:border-red-500' : ''"
      />
      <p v-if="addForm.errors.address" class="text-sm text-red-600">
        {{ addForm.errors.address[0] }}
      </p>
    </div>

    <!-- Contact Number -->
    <div>
      <Label for="add-contact">Contact Number</Label>
      <Input
        id="add-contact"
        v-model="addForm.contact_number"
        placeholder="+1-555-9811-376"
        :disabled="addForm.processing"
        required
        :class="addForm.errors.contact_number ? 'border-red-500 focus:border-red-500' : ''"
      />
      <p v-if="addForm.errors.contact_number" class="text-sm text-red-600">
        {{ addForm.errors.contact_number[0] }}
      </p>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-2">
      <Button variant="outline" @click="isAddOpen = false" :disabled="addForm.processing">
        Cancel
      </Button>
      <Button type="submit" :disabled="addForm.processing">
        Save
      </Button>
    </div>
  </form>
      </DrawerContent>
    </Drawer>

    <!-- Edit Drawer -->
    <Drawer v-model:open="isEditOpen">
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>Edit Customer</DrawerTitle>
          <DrawerDescription>Update the details below.</DrawerDescription>
        </DrawerHeader>

        <form class="p-4 space-y-4" @submit.prevent="submitEdit">
          <div>
            <Label for="edit-name">Name</Label>
            <Input id="edit-name" v-model="editForm.name" placeholder="Name" :disabled="editForm.processing" required />
            <p v-if="editForm.errors.name" class="text-sm text-red-600">{{ editForm.errors.name }}</p>
          </div>

          <div>
            <Label for="edit-email">Email</Label>
            <Input id="edit-email" v-model="editForm.email" type="email" placeholder="Email"
              :disabled="editForm.processing" required />
            <p v-if="editForm.errors.email" class="text-sm text-red-600">{{ editForm.errors.email }}</p>
          </div>

          <div>
            <Label for="edit-dob">Date of Birth</Label>
            <Input id="edit-dob" v-model="editForm.date_of_birth" type="date" placeholder="YYYY-MM-DD"
              :disabled="editForm.processing" required />
            <p v-if="editForm.errors.date_of_birth" class="text-sm text-red-600">{{ editForm.errors.date_of_birth }}</p>
          </div>

          <div>
            <Label for="edit-address">Address</Label>
            <Input id="edit-address" v-model="editForm.address" placeholder="Address" :disabled="editForm.processing"
              required />
            <p v-if="editForm.errors.address" class="text-sm text-red-600">{{ editForm.errors.address }}</p>
          </div>

          <div>
            <Label for="edit-contact">Contact Number</Label>
            <Input id="edit-contact" v-model="editForm.contact_number" placeholder="+1-555-9811-376"
              :disabled="editForm.processing" required />
            <p v-if="editForm.errors.contact_number" class="text-sm text-red-600">{{ editForm.errors.contact_number }}
            </p>
          </div>

          <div class="flex justify-end space-x-2">
            <Button variant="outline" @click="isEditOpen = false" :disabled="editForm.processing">Cancel</Button>
            <Button type="submit" :disabled="editForm.processing">Update</Button>
          </div>
        </form>
      </DrawerContent>
    </Drawer>


  </div>
</AppLayout>
</template>
