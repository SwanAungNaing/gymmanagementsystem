<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="" class="logo">
        <img
          src="assets/img/kaiadmin/logo_light.svg"
          alt="navbar brand"
          class="navbar-brand"
          height="20" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item active">
          <a
            href="../index.php"
            class="collapsed">
            <h2>Dashboard</h2>
          </a>
        </li>
        </li>

        <!-- Trainer -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#trainer"
            class="collapsed">
            <i class="fas fa-user-tie"></i>
            <p>Trainers</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="trainer">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="trainer_create">
                <a href="trainer_create.php">
                  <span class="sub-item">Trainers Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="trainer_list">
                <a href="trainer_list.php">
                  <span class="sub-item">Trainer Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        </li>

        <!-- Service  -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#service"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-handshake"></i>
            <p>Services</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="service">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="service_create">
                <a href="service_create.php">
                  <span class="sub-item">Services Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="service_list">
                <a href="service_list.php">
                  <span class="sub-item">Service Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Member -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#member"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-user-gear"></i>
            <p>Members</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="member">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="member_create">
                <a href="member_create.php">
                  <span class="sub-item">Members Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="member_list">
                <a href="member_list.php">
                  <span class="sub-item">Member Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Class -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#class"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-chalkboard-teacher"></i>
            <p>Classes</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="class">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="class_create">
                <a href="class_create.php">
                  <span class="sub-item">Classes Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="class_list">
                <a href="class_list.php">
                  <span class="sub-item">Class Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Class Member -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#class_member"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-users"></i>
            <p>Class Members</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="class_member">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="class_member_create">
                <a href="class_member_create.php">
                  <span class="sub-item">Class Members Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="class_member_list">
                <a href="class_member_list.php">
                  <span class="sub-item">Class Member Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        </li>

        <!-- Class_Payment -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#class_payment"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-credit-card"></i>
            <p>Class Payments</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="class_payment">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="class_payment_create">
                <a href="class_payment_create.php">
                  <span class="sub-item">Class Payments Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="class_payment_list">
                <a href="class_payment_list.php">
                  <span class="sub-item">Class Payment Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        </li>

        <!-- Attendance -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#attendance"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-clipboard-check"></i>
            <p>Attendances</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="attendance">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="attendance_create">
                <a href="attendance_create.php">
                  <span class="sub-item">Attendances Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="attendance_list">
                <a href="attendance_list.php">
                  <span class="sub-item">Attendance Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        </li>

        <!-- Member Weight -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#member_weight"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-weight"></i>
            <p>Member Weights</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="member_weight">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="member_weight_create">
                <a href="member_weight_create.php">
                  <span class="sub-item">Member Weights Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="member_weight_list">
                <a href="member_weight_list.php">
                  <span class="sub-item">Member Weight Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Brand_Name -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#brand_name"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-tags"></i>
            <p>Brand Names</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="brand_name">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="brand_name_create">
                <a href="brand_name_create.php">
                  <span class="sub-item">Brand Names Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="brand_name_list">
                <a href="brand_name_list.php">
                  <span class="sub-item">Brand Name Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- Equipment_Type -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#equipment_type"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-cogs"></i>
            <p>Equipment Types</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="equipment_type">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="equipment_type_create">
                <a href="equipment_type_create.php">
                  <span class="sub-item">Equipment Types Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="equipment_type_list">
                <a href="equipment_type_list.php">
                  <span class="sub-item">Equipment Type Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        </li>

        <!-- Equipment  -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#equipment"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-dumbbell"></i>
            <p>Equipments</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="equipment">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="equipment_create">
                <a href="equipment_create.php">
                  <span class="sub-item">Equipments Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="equipment_list">
                <a href="equipment_list.php">
                  <span class="sub-item">Equipment Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>

        <!-- E_Sale Order -->
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#e_sale_order"
            class="collapsed"
            aria-expanded="false">
            <i class="fas fa-shopping-cart"></i>
            <p>E-Sale Orders</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="e_sale_order">
            <ul class="nav nav-collapse">
              <li class="nav-each-link" data-value="e_sale_order_create">
                <a href="e_sale_order_create.php">
                  <span class="sub-item">E-Sale Orders Create</span>
                </a>
              </li>
              <li class="nav-each-link" data-value="e_sale_order_list">
                <a href="e_sale_order_list.php">
                  <span class="sub-item">E-Sale Order Lists</span>
                </a>
              </li>
            </ul>
          </div>
        </li>


        <!-- <li class="nav-section">
           <span class="sidebar-mini-icon">
             <i class="fa fa-ellipsis-h"></i>
           </span>
           <h4 class="text-section">Components</h4>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#base">
             <i class="fas fa-layer-group"></i>
             <p>Base</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="base">
             <ul class="nav nav-collapse">
               <li>
                 <a href="components/avatars.html">
                   <span class="sub-item">Avatars</span>
                 </a>
               </li>
               <li>
                 <a href="components/buttons.html">
                   <span class="sub-item">Buttons</span>
                 </a>
               </li>
               <li>
                 <a href="components/gridsystem.html">
                   <span class="sub-item">Grid System</span>
                 </a>
               </li>
               <li>
                 <a href="components/panels.html">
                   <span class="sub-item">Panels</span>
                 </a>
               </li>
               <li>
                 <a href="components/notifications.html">
                   <span class="sub-item">Notifications</span>
                 </a>
               </li>
               <li>
                 <a href="components/sweetalert.html">
                   <span class="sub-item">Sweet Alert</span>
                 </a>
               </li>
               <li>
                 <a href="components/font-awesome-icons.html">
                   <span class="sub-item">Font Awesome Icons</span>
                 </a>
               </li>
               <li>
                 <a href="components/simple-line-icons.html">
                   <span class="sub-item">Simple Line Icons</span>
                 </a>
               </li>
               <li>
                 <a href="components/typography.html">
                   <span class="sub-item">Typography</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#sidebarLayouts">
             <i class="fas fa-th-list"></i>
             <p>Sidebar Layouts</p>

<span class="caret"></span>
           </a>
           <div class="collapse" id="sidebarLayouts">
             <ul class="nav nav-collapse">
               <li>
                 <a href="sidebar-style-2.html">
                   <span class="sub-item">Sidebar Style 2</span>
                 </a>
               </li>
               <li>
                 <a href="icon-menu.html">
                   <span class="sub-item">Icon Menu</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#forms">
             <i class="fas fa-pen-square"></i>
             <p>Forms</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="forms">
             <ul class="nav nav-collapse">
               <li>
                 <a href="forms/forms.html">
                   <span class="sub-item">Basic Form</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#tables">
             <i class="fas fa-table"></i>
             <p>Tables</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="tables">
             <ul class="nav nav-collapse">
               <li>
                 <a href="tables/tables.html">
                   <span class="sub-item">Basic Table</span>
                 </a>
               </li>
               <li>
                 <a href="tables/datatables.html">
                   <span class="sub-item">Datatables</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#maps">
             <i class="fas fa-map-marker-alt"></i>
             <p>Maps</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="maps">
             <ul class="nav nav-collapse">
               <li>
                 <a href="maps/googlemaps.html">
                   <span class="sub-item">Google Maps</span>
                 </a>
               </li>
               <li>
                 <a href="maps/jsvectormap.html">
                   <span class="sub-item">Jsvectormap</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#charts">
             <i class="far fa-chart-bar"></i>
             <p>Charts</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="charts">
             <ul class="nav nav-collapse">
               <li>
                 <a href="charts/charts.html">
                   <span class="sub-item">Chart Js</span>
                 </a>
               </li>
               <li>
                 <a href="charts/sparkline.html">
                   <span class="sub-item">Sparkline</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
         <li class="nav-item">
           <a href="widgets.html">
             <i class="fas fa-desktop"></i>
             <p>Widgets</p>
             <span class="badge badge-success">4</span>
           </a>
         </li>
         <li class="nav-item">
           <a href="../../documentation/index.html">
             <i class="fas fa-file"></i>
             <p>Documentation</p>
             <span class="badge badge-secondary">1</span>
           </a>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#submenu">
             <i class="fas fa-bars"></i>
             <p>Menu Levels</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="submenu">
             <ul class="nav nav-collapse">
               <li>
                 <a data-bs-toggle="collapse" href="#subnav1">
                   <span class="sub-item">Level 1</span>

Jai Nom Pha, [7/15/2025 1:43 PM]
<span class="caret"></span>
                 </a>
                 <div class="collapse" id="subnav1">
                   <ul class="nav nav-collapse subnav">
                     <li>
                       <a href="#">
                         <span class="sub-item">Level 2</span>
                       </a>
                     </li>
                     <li>
                       <a href="#">
                         <span class="sub-item">Level 2</span>
                       </a>
                     </li>
                   </ul>
                 </div>
               </li>
               <li>
                 <a data-bs-toggle="collapse" href="#subnav2">
                   <span class="sub-item">Level 1</span>
                   <span class="caret"></span>
                 </a>
                 <div class="collapse" id="subnav2">
                   <ul class="nav nav-collapse subnav">
                     <li>
                       <a href="#">
                         <span class="sub-item">Level 2</span>
                       </a>
                     </li>
                   </ul>
                 </div>
               </li>
               <li>
                 <a href="#">
                   <span class="sub-item">Level 1</span>
                 </a>
               </li>
             </ul>
           </div>
         </li> -->
      </ul>
    </div>
  </div>
</div>