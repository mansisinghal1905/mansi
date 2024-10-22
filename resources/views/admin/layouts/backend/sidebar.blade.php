<!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('admin.adminDashboard') }}" class="b-brand">
                    <!-- ========   change your logo hear   ============ -->
                    <img src="{{ asset('public/assets/images/logo.png')}}" alt=""style="height: 50px;width: 200px;" class="logo logo-lg" />
                    <img src="{{ asset('public/assets/images/logo-icon.png')}}" alt="" class="logo logo-sm" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                   
                    <li class="nxl-item">
                        <a href="{{ route('admin.adminDashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboards</span>
                        </a>
                    </li>


                    @can('Customer-Management')
                     <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext"> Customers</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.users.index') }}"> Clients Management</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.developers.index') }}">Developer Management</a></li>
                        </ul>
                    </li> 
                    @endcan

                    @can('Designation-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.category.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layout"></i></span>
                            <span class="nxl-mtext">Designation Management</span>
                        </a>  
                    </li>
                    @endcan

                    @can('SendPurposal-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.sendpurposal.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Send Purposal Management</span>
                        </a>  
                    </li>
                    @endcan

                    @can('ProjectStatus-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.projectstatus.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layout"></i></span>
                            <span class="nxl-mtext">project Status Management</span>
                        </a>  
                    </li>
                    @endcan
                    @can('Project-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.projects.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Projects Management</span>
                        </a>  
                    </li>
                    @endcan
                    @can('Project-Assign-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.projects-assign.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Projects Assign Management</span>
                        </a>  
                    </li>
                    @endcan

                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.tasks.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">TimeSheet Management</span>
                        </a>  
                    </li>

                    <!-- @can('Task-Assign-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.taskassign.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Task Assign Management</span>
                        </a>  
                    </li>
                    @endcan -->

                    
                    
                    <!-- @can('Invoice-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.invoice.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Invoice Management</span>
                        </a>  
                    </li>
                    @endcan -->
                    @can('Invoice-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext"> Invoice Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.payments.index') }}"> Payment</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.invoice.index') }}">Invoice</a></li>
                        </ul>
                    </li> 
                    @endcan

                    
                    
                    @can('Role-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.roles.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Role Management</span>
                        </a>
                    </li>
                    @endcan


                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext"> Host Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                        @if(Auth::user()->role == 1)
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.host-customer.index') }}"> Hosting Customers</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.hostpayments.index') }}">Payment</a></li>
                         @endif   
                            <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.ticket-system.index') }}">Ticket System</a></li>
                        </ul>
                    </li>

                    @can('Chat-Management')
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.chat.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Chat Management</span>
                        </a>
                    </li>
                    @endcan

                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.attendances.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Attendance Management</span>
                        </a>
                    </li>
                </ul>
               
            </div>
        </div>
    </nav>
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->