<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายการพนักงาน</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">Add+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>ชื่อ-สกุล</th>
          <th>แผนก</th>
          <th>เงินเดือน</th>
          <th>สถานะ</th>
          <th>รูปภาพ</th>
          <th>การจัดการ</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="emp in employees" :key="emp.emp_id">
          <td>{{ emp.emp_id }}</td>
          <td>{{ emp.full_name }}</td>
          <td>{{ emp.department }}</td>
          <td>{{ emp.salary }}</td>
          <td>
            <span v-if="emp.active == 1">ปกติ</span>
            <span v-else>ลาออก</span>
          </td>
          <td>
            <img
              v-if="emp.image"
              :src="'http://127.0.0.1/app-vue01/php_api/uploads/' + emp.image"
              width="100"
            />
          </td>
          <td>
            <button
              class="btn btn-warning btn-sm me-2"
              @click="openEditModal(emp)"
            >
              แก้ไข
            </button>
            <button
              class="btn btn-danger btn-sm"
              @click="deleteEmp(emp.emp_id)"
            >
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal เพิ่ม / แก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ isEditMode ? "แก้ไขพนักงาน" : "เพิ่มพนักงานใหม่" }}
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <div class="modal-body">
            <form @submit.prevent="saveEmp">
              <div class="mb-3">
                <label class="form-label">ชื่อ-สกุล</label>
                <input
                  v-model="editForm.full_name"
                  type="text"
                  class="form-control"
                  required
                />
              </div>

              <!-- แผนก -->
              <div class="mb-3">
                <label class="form-label">แผนก</label>
                <select
                  v-model="editForm.department"
                  class="form-control"
                  required
                >
                  <option value="IT">IT</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Planing">Planing</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">เงินเดือน</label>
                <input
                  v-model="editForm.salary"
                  type="number"
                  step="0.01"
                  class="form-control"
                  required
                />
              </div>

              <!-- สถานะ -->
              <div class="mb-3">
                <label class="form-label">สถานะ</label>
                <select
                  v-model.number="editForm.active"
                  class="form-control"
                  required
                >
                  <option :value="1">ปกติ</option>
                  <option :value="0">ลาออก</option>
                </select>
              </div>

              <!-- รูป -->
              <div class="mb-3">
                <label class="form-label">รูปภาพ</label>
                <input
                  type="file"
                  @change="handleFileUpload"
                  class="form-control"
                  :required="!isEditMode"
                />

                <div v-if="isEditMode && editForm.image" class="mt-2">
                  <p>รูปเดิม:</p>
                  <img
                    :src="
                      'http://127.0.0.1/app-vue01/php_api/uploads/' + editForm.image
                    "
                    width="100"
                  />
                </div>
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกพนักงานใหม่" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "EmpList",
  setup() {
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const isEditMode = ref(false);

    const editForm = ref({
      emp_id: null,
      full_name: "",
      department: "",
      salary: "",
      active: "",
      image: "",
    });

    const newImageFile = ref(null);
    let modalInstance = null;

    // โหลดข้อมูลพนักงาน
    const fetchEmployees = async () => {
      try {
        const res = await fetch("http://127.0.0.1/app-vue01/php_api/emp_crud_img.php");
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เพิ่มพนักงาน
    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        emp_id: null,
        full_name: "",
        department: "",
        salary: "",
        active: "",
        image: "",
      };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();

      const fileInput = modalEl.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = "";
    };

    // แก้ไขพนักงาน
    const openEditModal = (emp) => {
      isEditMode.value = true;
      editForm.value = { ...emp };
      newImageFile.value = null;

      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    const handleFileUpload = (e) => {
      newImageFile.value = e.target.files[0];
    };

    // เพิ่ม / แก้ไข
    const saveEmp = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("emp_id", editForm.value.emp_id);

      formData.append("full_name", editForm.value.full_name);
      formData.append("department", editForm.value.department);
      formData.append("salary", editForm.value.salary);
      formData.append("active", editForm.value.active);
      if (newImageFile.value) formData.append("image", newImageFile.value);

      try {
        const res = await fetch("http://127.0.0.1/app-vue01/php_api/emp_crud_img.php", {
          method: "POST",
          body: formData,
        });
        const result = await res.json();
        alert(result.message || result.error);
        fetchEmployees();
        modalInstance.hide();
      } catch (err) {
        alert(err.message);
      }
    };

    // ลบพนักงาน
    const deleteEmp = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("emp_id", id);

      try {
        const res = await fetch("http://127.0.0.1/app-vue01/php_api/emp_crud_img.php", {
          method: "POST",
          body: formData,
        });
        const result = await res.json();
        alert(result.message || result.error);
        employees.value = employees.value.filter((e) => e.emp_id !== id);
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      handleFileUpload,
      saveEmp,
      deleteEmp,
    };
  },
};
</script>
