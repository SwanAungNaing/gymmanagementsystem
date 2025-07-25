<footer class="footer">
  <div class="container-fluid d-flex justify-content-between">
    <nav class="pull-left">
      <ul class="nav">
        <li class="nav-item">
          <a class="nav-link" href="#">
            Gym Management Team
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"> Help </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"> Licenses </a>
        </li>
      </ul>
    </nav>
    <div class="copyright">
      2025, made with <i class="fa fa-heart heart text-danger"></i> by
      <a href="#">Gym Management Team</a>
    </div>
    <div>
      Distributed by
      <a target="_blank" href="https://themewagon.com/">Gym Management Team</a>.
    </div>
  </div>
</footer>
</div>

<!-- Custom template | don't include it in your project! -->
<!-- <div class="custom-template">
  <div class="title">Settings</div>
  <div class="custom-content">
    <div class="switcher">
      <div class="switch-block">
        <h4>Logo Header</h4>
        <div class="btnSwitch">
          <button
            type="button"
            class="selected changeLogoHeaderColor"
            data-color="dark"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="blue"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="purple"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="light-blue"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="green"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="orange"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="red"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="white"></button>
          <br />
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="dark2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="blue2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="purple2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="light-blue2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="green2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="orange2"></button>
          <button
            type="button"
            class="changeLogoHeaderColor"
            data-color="red2"></button>
        </div>
      </div>
      <div class="switch-block">
        <h4>Navbar Header</h4>
        <div class="btnSwitch">
          <button
            type="button"
            class="changeTopBarColor"
            data-color="dark"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="blue"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="purple"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="light-blue"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="green"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="orange"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="red"></button>
          <button
            type="button"
            class="selected changeTopBarColor"
            data-color="white"></button>
          <br />
          <button
            type="button"
            class="changeTopBarColor"
            data-color="dark2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="blue2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="purple2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="light-blue2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="green2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="orange2"></button>
          <button
            type="button"
            class="changeTopBarColor"
            data-color="red2"></button>
        </div>
      </div>
      <div class="switch-block">
        <h4>Sidebar</h4>
        <div class="btnSwitch">
          <button
            type="button"
            class="changeSideBarColor"
            data-color="white"></button>
          <button
            type="button"
            class="selected changeSideBarColor"
            data-color="dark"></button>
          <button
            type="button"
            class="changeSideBarColor"
            data-color="dark2"></button>
        </div>
      </div>
    </div>
  </div>
  <div class="custom-toggle">
    <i class="icon-settings"></i>
  </div>
</div> -->
<!-- End Custom template -->
</div>
<!--   Core JS Files   -->
<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Chart JS -->
<script src="assets/js/plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="assets/js/plugin/chart-circle/circles.min.js"></script>

<!-- Datatables -->
<script src="assets/js/plugin/datatables/datatables.min.js"></script>

<!-- Bootstrap Notify -->
<script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- jQuery Vector Maps -->
<script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
<script src="assets/js/plugin/jsvectormap/world.js"></script>

<!-- Sweet Alert -->
<script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="assets/js/kaiadmin.min.js"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="assets/js/setting-demo.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="assets/js/demo.js"></script> -->
<script>
  $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#177dff",
    fillColor: "rgba(23, 125, 255, 0.14)",
  });

  $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#f3545d",
    fillColor: "rgba(243, 84, 93, .14)",
  });

  $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#ffa534",
    fillColor: "rgba(255, 165, 52, .14)",
  });

  $(document).ready(() => {
    let urlArr = window.location.href.split("/")
    let lastIndex = urlArr[urlArr.length - 1]

    $(".nav-each-link").on("click", function(e) {
      localStorage.removeItem("open_sidebar")
      localStorage.setItem("open_sidebar", $(this).data("value"));
    });
    let status = localStorage.getItem("open_sidebar") + ".php" == lastIndex
    if (status) {
      const value = localStorage.getItem("open_sidebar");
      const getID = value.substring(0, value.lastIndexOf("_"));
      document.getElementById(getID).classList.add("show");
    }

  })
</script>
</body>

</html>