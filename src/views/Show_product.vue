<template>
  <div class="container mt-0">

    <!-- ✅ Hero / Banner -->
    <div class="bg-light p-5 mb-4 shadow-sm rounded">
      <div class="container text-center">
        <h2 class="fw-bold">รายการสินค้า</h2>
      </div>
    </div>

    <!-- ✅ Loading -->
    <div v-if="loading" class="text-center my-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-3">กำลังโหลดสินค้า...</p>
    </div>

    <!-- ✅ Error -->
    <div v-else-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <!-- ✅ Product Section -->
    <div v-else class="mb-5">

      <div class="row">
        <div 
          class="col-md-3 mb-4" 
          v-for="product in Alldata" 
          :key="product.product_id"
        >
          <div class="card h-100 shadow-sm">

            <!-- ✅ รูปสินค้า -->
            <img 
              :src="getImage(product.image)" 
              class="card-img-top"
              style="height: 200px; object-fit: cover;"
            />

            <div class="card-body">
              <h6 class="card-title">
                {{ product.product_name }}
              </h6>

              <p class="text-primary fw-bold">
                {{ product.price }} บาท
              </p>
            </div>

            <div class="card-footer bg-white border-0">

              <!-- ✅ ปุ่ม Detail -->
              <router-link 
                :to="'/ProductDetail?id=' + product.product_id"
                class="btn btn-sm btn-outline-primary w-100 mb-2"
              >
                ดูรายละเอียด
              </router-link>

              <button class="btn btn-sm btn-primary w-100">
                สั่งซื้อ
              </button>

            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "Home",
  setup() {

    const Alldata = ref([]);
    const loading = ref(true);
    const error = ref(null);

    // ✅ สร้าง URL รูป
    const getImage = (image) => {
      if (!image) {
        return "https://via.placeholder.com/300x200?text=No+Image";
      }
      return `http://localhost/app-vue01/php_api/uploads/${image}`;
    };

    // ✅ Fetch Data
    const fetchData = async () => {
      try {

        const response = await fetch(
          "http://localhost/app-vue01/php_api/show_product_home.php"
        );

        if (!response.ok) {
          throw new Error("โหลดข้อมูลสินค้าไม่ได้");
        }

        const data = await response.json();

        if (!data || data.length === 0) {
          throw new Error("ไม่มีสินค้าในระบบ");
        }

        Alldata.value = data;

      } catch (err) {
        error.value = err.message;
        console.error(err.message);
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchData();
    });

    return {
      Alldata,
      loading,
      error,
      getImage
    };
  }
};
</script>