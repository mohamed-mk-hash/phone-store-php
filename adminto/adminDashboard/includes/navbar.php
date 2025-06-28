<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <form method="GET">
                    <div class="input-group input-group-outline">
                       
                        <input type="text" name="search" placeholder="tapez ici" class="form-control" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    </div>
                    <button type="submit" style="display:none;"></button> 
                </form>
            </div>
        </div>
    </div>
</nav>
