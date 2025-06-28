<?php
session_start();
include('includes/header.php');
?>
  
<div class="py-5">
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-6">
            <div class="card mt-5">
               <div class="card-header">
                  <h4>Formulaire de Connexion</h4>
               </div>
               <div class="card-body p-3">
                  <form action="config/functions/authcode.php" method="POST">
                     <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Adresse Email</label>
                        <input required type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Entrez votre email">
                     </div>
                     <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Mot de passe</label>
                        <input required type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Entrez votre mot de passe">
                     </div>
                     <?php if(isset($_SESSION['message'])  ): ?>
               <div class="alert text-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?> 
                   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>
               <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
                     <button type="submit" name="login_btn" class="btn btn-primary">Se Connecter</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include('includes/footer.php'); ?>

<style>
   body {
      background: linear-gradient(to left, red, black);
   }
</style>