<style type="text/css">
.nav-link[data-toggle].collapsed:after {
    content: "▾";
}
.nav-link[data-toggle]:not(.collapsed):after {
    content: "▴";
}
</style>
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-2 collapse d-md-flex bg-light pt-2 h-100" id="sidebar">
            <ul class="nav flex-column flex-nowrap">
                <li class="nav-item">
                    <?php if(isset($login)){ ?>
                    <a class="navbar-brand" href="#"><?php echo $login; ?></a>
                    <?php }else{ ?>
                    <a class="navbar-brand" href="#"><?php echo $titulo; ?></a>
                    <?php } ?>
                </li>
                <!--<li class="nav-item">
                    <a class="nav-link collapsed" href="#submenu1" data-toggle="collapse" data-target="#submenu1">Reports</a>
                    <div class="collapse" id="submenu1" aria-expanded="false">
                        <ul class="flex-column pl-2 nav">
                            <li class="nav-item"><a class="nav-link py-0" href="#">Orders</a></li>
                            <li class="nav-item">
                                <a class="nav-link collapsed py-1" href="#submenu1sub1" data-toggle="collapse" data-target="#submenu1sub1">Customers</a>
                                <div class="collapse" id="submenu1sub1" aria-expanded="false">
                                    <ul class="flex-column nav pl-4">
                                        <li class="nav-item">
                                            <a class="nav-link p-1" href="#">
                                                <i class="fa fa-fw fa-clock-o"></i> Daily
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link p-1" href="#">
                                                <i class="fa fa-fw fa-dashboard"></i> Dashboard
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link p-1" href="#">
                                                <i class="fa fa-fw fa-bar-chart"></i> Charts
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link p-1" href="#">
                                                <i class="fa fa-fw fa-compass"></i> Areas
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>-->
                <?php if(isset($nav)){ foreach ($nav as $value){ ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url($value); ?>"><?php echo $value?></a></li>
                <?php } ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('Login/salir'); ?>">Cierre de sesion</a></li>
                <?php }else{ ?>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('inicio'); ?>">pagina principal</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo base_url('login'); ?>">Inicio de sesion</a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="col pt-2">
        