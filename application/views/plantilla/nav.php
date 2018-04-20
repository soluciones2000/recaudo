<style type="text/css">

    th { font-size: 12px; }
    td { font-size: 12px; }

    /* start of navbar side menu */
    #sidebar .list-group-item {
        border-radius: 0;
        /*background-color: #333;
        color: #ccc;*/
        border-left: 0;
        border-right: 0;
        border-color: #2c2c2c;
        white-space: nowrap;
    }

    /* highlight active menu */
    #sidebar .list-group-item:not(.collapsed) {
        /*background-color: #222;*/
    }

    /* closed state */
    #sidebar .list-group .list-group-item[aria-expanded="false"]::after {
      content: " \f0d7";
      font-family: FontAwesome;
      display: inline;
      text-align: right;
      padding-left: 5px;
    }

    /* open state */
    #sidebar .list-group .list-group-item[aria-expanded="true"] {
      /*background-color: #222;*/
    }
    #sidebar .list-group .list-group-item[aria-expanded="true"]::after {
      content: " \f0da";
      font-family: FontAwesome;
      display: inline;
      text-align: right;
      padding-left: 5px;
    }

    /* level 1*/
    #sidebar .list-group .collapse .list-group-item  {
      padding-left: 20px;
    }

    /* level 2*/
    #sidebar .list-group .collapse > .collapse .list-group-item {
      padding-left: 30px;
    }

    /* level 3*/
    #sidebar .list-group .collapse > .collapse > .collapse .list-group-item {
      padding-left: 40px;
    }

    @media (max-width:48em) {
        /* overlay sub levels on small screens */
        #sidebar .list-group .collapse.in, #sidebar .list-group .collapsing {
            position: absolute;
            z-index: 1;
            width: 190px;
        }
        #sidebar .list-group > .list-group-item {
            text-align: center;
            padding: .75rem .5rem;
        }
        /* hide caret icons of top level when collapsed */
        #sidebar .list-group > .list-group-item[aria-expanded="true"]::after,
        #sidebar .list-group > .list-group-item[aria-expanded="false"]::after {
            display:none;
        }
    }

    /* change transition animation to width when entire sidebar is toggled */
    #sidebar.collapse {
      -webkit-transition-timing-function: ease;
           -o-transition-timing-function: ease;
              transition-timing-function: ease;
      -webkit-transition-duration: .2s;
           -o-transition-duration: .2s;
              transition-duration: .2s;
    }

    #sidebar.collapsing {
      opacity: 0.8;
      width: 0;
      -webkit-transition-timing-function: ease-in;
           -o-transition-timing-function: ease-in;
              transition-timing-function: ease-in;
      -webkit-transition-property: width;
           -o-transition-property: width;
              transition-property: width;

    }
    /* end of navbar side menu */
</style>
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-md-3 col-xs-1 p-l-0 p-r-0 collapsed in bg-light" id="sidebar">
            <div class="list-group panel">
                <?php if(isset($login)){ ?>
                <a class="navbar-brand" href="#"><?php echo $login; ?></a>
                <?php }else{ ?>
                <a class="navbar-brand" href="#"><?php echo $titulo; ?></a>
                <?php } ?>
                <?php if(isset($nav)){ foreach ($nav as $value){ ?>
                <a class="list-group-item" href="<?php echo base_url($value); ?>"><?php echo $value?></a>
                <?php } ?>
                <a class="list-group-item" href="<?php echo base_url('Login/salir'); ?>">Cierre de sesion</a>
                <?php }else{ ?>
                <a class="list-group-item" href="<?php echo base_url('inicio'); ?>">pagina principal</a>
                <a class="list-group-item" href="<?php echo base_url('login'); ?>">Inicio de sesion</a>
                <?php } ?>
                <!--
                <a href="#menu1" class="list-group-item collapsed" data-toggle="collapse" data-parent="#sidebar" aria-expanded="false"><i class="fa fa-dashboard"></i> <span class="hidden-sm-down">Item 1</span> </a>
                <div class="collapse" id="menu1">
                    <a href="#menu1sub1" class="list-group-item" data-toggle="collapse" aria-expanded="false">Subitem 1 </a>
                    <div class="collapse" id="menu1sub1">
                        <a href="#" class="list-group-item" data-parent="#menu1sub1">Subitem 1 a</a>
                        <a href="#" class="list-group-item" data-parent="#menu1sub1">Subitem 2 b</a>
                        <a href="#menu1sub1sub1" class="list-group-item" data-toggle="collapse" aria-expanded="false">Subitem 3 c </a>
                        <div class="collapse" id="menu1sub1sub1">
                            <a href="#" class="list-group-item" data-parent="#menu1sub1sub1">Subitem 3 c.1</a>
                            <a href="#" class="list-group-item" data-parent="#menu1sub1sub1">Subitem 3 c.2</a>
                        </div>
                        <a href="#" class="list-group-item" data-parent="#menu1sub1">Subitem 4 d</a>
                        <a href="#menu1sub1sub2" class="list-group-item" data-toggle="collapse"  aria-expanded="false">Subitem 5 e </a>
                        <div class="collapse" id="menu1sub1sub2">
                            <a href="#" class="list-group-item" data-parent="#menu1sub1sub2">Subitem 5 e.1</a>
                            <a href="#" class="list-group-item" data-parent="#menu1sub1sub2">Subitem 5 e.2</a>
                        </div>
                    </div>
                    <a href="#" class="list-group-item" data-parent="#menu1">Subitem 2</a>
                    <a href="#" class="list-group-item" data-parent="#menu1">Subitem 3</a>
                </div>
                <a href="#" class="list-group-item collapsed" data-parent="#sidebar"><i class="fa fa-film"></i> <span class="hidden-sm-down">Item 2</span></a>
                <a href="#menu3" class="list-group-item collapsed" data-toggle="collapse" data-parent="#sidebar" aria-expanded="false"><i class="fa fa-book"></i> <span class="hidden-sm-down">Item 3 </span></a>
                <div class="collapse" id="menu3">
                    <a href="#" class="list-group-item" data-parent="#menu3">3.1</a>
                    <a href="#menu3sub2" class="list-group-item" data-toggle="collapse" aria-expanded="false">3.2 </a>
                    <div class="collapse" id="menu3sub2">
                        <a href="#" class="list-group-item" data-parent="#menu3sub2">3.2 a</a>
                        <a href="#" class="list-group-item" data-parent="#menu3sub2">3.2 b</a>
                        <a href="#" class="list-group-item" data-parent="#menu3sub2">3.2 c</a>
                    </div>
                    <a href="#" class="list-group-item" data-parent="#menu3">3.3</a>
                </div>
                <a href="#" class="list-group-item collapsed" data-parent="#sidebar"><i class="fa fa-heart"></i> <span class="hidden-sm-down">Item 4</span></a>
                <a href="#" class="list-group-item collapsed" data-parent="#sidebar"><i class="fa fa-list"></i> <span class="hidden-sm-down">Item 5</span></a>
                -->
            </div>
        </div>
