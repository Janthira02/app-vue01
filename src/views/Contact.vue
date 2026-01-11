<template>
  <div class="container">
    <h1 class="text-primary">Contact to Janthira Phosarach</h1>
    <h3>Tel.085-517-2774</h3>
    <img alt="Vue logo" src="../assets/me1.jpg" />
    <br />
    <br />
    <br />

    <div class="container">
      <div class="row">
        <!--Col1-->
        <div class="col-sm-6">
          <img alt="Vue logo" src="../assets/cat.jpg" />
        </div>

        <!--Col2-->
        <div class="col-sm-6">
          <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label"
              >Email address</label
            >
            <input
              type="email"
              class="form-control"
              id="exampleFormControlInput1"
              placeholder="name@example.com"
            />
          </div>


          <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label"
              >Detail</label
            >
            <textarea
              class="form-control"
              id="exampleFormControlTextarea1"
              rows="3"
            ></textarea>
          </div>


          <div class="mb-3 text-center">
            <button type="button" class="btn btn-primary">Submit</button>&nbsp;
            <button type="button" class="btn btn-danger">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
