<script setup lang="ts">
import { ref, reactive } from 'vue'
import api from '@/Api/Axios'
import { toast } from 'vue-sonner'

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog'

import {
  Drawer,
  DrawerContent,
  DrawerHeader,
  DrawerTitle,
  DrawerDescription,
  DrawerFooter,
  DrawerClose,
} from '@/components/ui/drawer'

import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Customer {
  id: number | string
  name: string
  email: string
  phone: string
  date_of_birth: string
  address: string
  contact_number: string

}

const customers = ref<Customer[]>([])

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
  errors: {} as Record<string, string>,
  processing: false,
})

// Fetch customers on mounted
async function fetchCustomers() {
  try {
    const response = await api.get('/customers')
    customers.value = response.data.data // <== Here is the fix: get the array inside the response
  } catch {
    toast.error('Failed to load customers')
  }
}

fetchCustomers()

function openAdd() {
  addForm.name = ''
  addForm.email = ''
  addForm.phone = ''
  addForm.errors = {}
  addForm.processing = false
  isAddOpen.value = true
}

function openEdit(customer: Customer) {
  editId.value = customer.id
  editForm.name = customer.name
  editForm.email = customer.email
  editForm.phone = customer.phone
  editForm.date_of_birth = customer.date_of_birth
  editForm.address = customer.address
  editForm.contact_number = customer.contact_number
  editForm.errors = {}
  editForm.processing = false
  isEditOpen.value = true
}

async function submitAdd() {
  addForm.processing = true
  addForm.errors = {}
  try {
    await api.post('/customers', {
      name: addForm.name,
      email: addForm.email,
      phone: addForm.phone,
      date_of_birth: addForm.date_of_birth,
      address: addForm.address,
      contact_number: addForm.contact_number,
    })
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
    await api.put(`/customers/${editId.value}`, {
      name: editForm.name,
      email: editForm.email,
      phone: editForm.phone,
      date_of_birth: editForm.date_of_birth,
      address: editForm.address,
      contact_number: editForm.contact_number,

    })
    toast.success('Customer updated')
    isEditOpen.value = false
    editId.value = null
    await fetchCustomers()
  } catch (error: any) {
    if (error.response?.data?.errors) {
      editForm.errors = error.response.data.errors
    } else {
      toast.error('Failed to update customer')
    }
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
  <div class="p-4 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold">Customers</h2>
      <Button size="sm" variant="secondary" @click="openAdd">Add Customer</Button>
    </div>

    <Table class="w-full table-fixed">
      <TableHeader>
        <TableRow>
          <TableHead class="w-1/5 px-4 py-2">Name</TableHead>
          <TableHead class="w-1/5 px-4 py-2">Email</TableHead>
          <TableHead class="w-1/5 px-4 py-2">DOB</TableHead>
          <TableHead class="w-1/5 px-4 py-2">Address</TableHead>
          <TableHead class="w-1/5 px-4 py-2">Contact</TableHead>
          <TableHead class="w-1/6 px-4 py-2">Actions</TableHead>
        </TableRow>
      </TableHeader>

      <TableBody v-if="customers.length">
        <TableRow v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50">
          <TableCell class="px-4 py-2 font-medium truncate">{{ customer.name }}</TableCell>
          <TableCell class="px-4 py-2 truncate">{{ customer.email }}</TableCell>
          <TableCell class="px-4 py-2 truncate">{{ customer.date_of_birth }}</TableCell>
          <TableCell class="px-4 py-2 truncate">{{ customer.address }}</TableCell>
          <TableCell class="px-4 py-2 truncate">{{ customer.contact_number }}</TableCell>
          <TableCell class="px-4 py-2 space-x-2">
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

    <!-- Add Drawer -->
    <Drawer v-model:open="isAddOpen">
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>Add Customer</DrawerTitle>
          <DrawerDescription>Fill in the details below.</DrawerDescription>
        </DrawerHeader>

        <form class="p-4 space-y-4" @submit.prevent="submitAdd">
          <div>
            <Label for="add-name">Name</Label>
            <Input
              id="add-name"
              v-model="addForm.name"
              placeholder="Name"
              :disabled="addForm.processing"
              required
            />
            <p v-if="addForm.errors.name" class="text-sm text-red-600">{{ addForm.errors.name }}</p>
          </div>

          <div>
            <Label for="add-email">Email</Label>
            <Input
              id="add-email"
              v-model="addForm.email"
              type="email"
              placeholder="Email"
              :disabled="addForm.processing"
              required
            />
            <p v-if="addForm.errors.email" class="text-sm text-red-600">{{ addForm.errors.email }}</p>
          </div>

          <div>
            <Label for="add-dob">Date of Birth</Label>
            <Input
              id="add-dob"
              v-model="addForm.date_of_birth"
              type="date"
              placeholder="YYYY-MM-DD"
              :disabled="addForm.processing"
              required
            />
            <p v-if="addForm.errors.date_of_birth" class="text-sm text-red-600">{{ addForm.errors.date_of_birth }}</p>
          </div>

          <div>
            <Label for="add-address">Address</Label>
            <Input
              id="add-address"
              v-model="addForm.address"
              placeholder="Address"
              :disabled="addForm.processing"
              required
            />
            <p v-if="addForm.errors.address" class="text-sm text-red-600">{{ addForm.errors.address }}</p>
          </div>

          <div>
            <Label for="add-contact">Contact Number</Label>
            <Input
              id="add-contact"
              v-model="addForm.contact_number"
              placeholder="+1-555-9811-376"
              :disabled="addForm.processing"
              required
            />
            <p v-if="addForm.errors.contact_number" class="text-sm text-red-600">{{ addForm.errors.contact_number }}</p>
          </div>

          <div class="flex justify-end space-x-2">
            <Button variant="outline" @click="isAddOpen = false" :disabled="addForm.processing">Cancel</Button>
            <Button type="submit" :disabled="addForm.processing">Save</Button>
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
            <Input
              id="edit-name"
              v-model="editForm.name"
              placeholder="Name"
              :disabled="editForm.processing"
              required
            />
            <p v-if="editForm.errors.name" class="text-sm text-red-600">{{ editForm.errors.name }}</p>
          </div>

          <div>
            <Label for="edit-email">Email</Label>
            <Input
              id="edit-email"
              v-model="editForm.email"
              type="email"
              placeholder="Email"
              :disabled="editForm.processing"
              required
            />
            <p v-if="editForm.errors.email" class="text-sm text-red-600">{{ editForm.errors.email }}</p>
          </div>

          <div>
            <Label for="edit-dob">Date of Birth</Label>
            <Input
              id="edit-dob"
              v-model="editForm.date_of_birth"
              type="date"
              placeholder="YYYY-MM-DD"
              :disabled="editForm.processing"
              required
            />
            <p v-if="editForm.errors.date_of_birth" class="text-sm text-red-600">{{ editForm.errors.date_of_birth }}</p>
          </div>

          <div>
            <Label for="edit-address">Address</Label>
            <Input
              id="edit-address"
              v-model="editForm.address"
              placeholder="Address"
              :disabled="editForm.processing"
              required
            />
            <p v-if="editForm.errors.address" class="text-sm text-red-600">{{ editForm.errors.address }}</p>
          </div>

          <div>
            <Label for="edit-contact">Contact Number</Label>
            <Input
              id="edit-contact"
              v-model="editForm.contact_number"
              placeholder="+1-555-9811-376"
              :disabled="editForm.processing"
              required
            />
            <p v-if="editForm.errors.contact_number" class="text-sm text-red-600">{{ editForm.errors.contact_number }}</p>
          </div>

          <div class="flex justify-end space-x-2">
            <Button variant="outline" @click="isEditOpen = false" :disabled="editForm.processing">Cancel</Button>
            <Button type="submit" :disabled="editForm.processing">Update</Button>
          </div>
        </form>
      </DrawerContent>
    </Drawer>
  </div>
</template>
