<style>
    button{
        width: 150px; border: solid 1px black; border-radius: 10px; height:35px; margin: 10px; background-color:gray;
    }
    button:hover{
        background-color: white;
    }

</style>
<div class="container" style="height: 750px;">
<div class="row">
<div class="col-md-3">

    <p style="margin-bottom: 150px;"></p>

    <center>
    <h2><strong style="font-size:35px">Üdvözöllek</strong>  az Online Sakk Oldalunkon!</h2>

<?php if (isset($_SESSION['user'])): ?>
    <p>Szia, <strong><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>!</p>
    <p>Kattints a "Sakk Játék" gombra egy új partiért, vagy nézd meg a "Profil" oldaladat a korábbi meccseidért.</p>
<?php else: ?>
    <br>
    <p>A meccseid mentéséhez és a profilod megtekintéséhez kérlek, jelentkez be vagy regisztrálj! <br>
    <a href="index.php?page=login" ><button ><strong>Bejelentkezés</strong></button></a> <br>
    
    
    <a href="index.php?page=register"><button style=" hover:"><strong>Regisztráció</strong></button></a>
    </center>
    </p>
<?php endif; ?>
</div>

<div class="col-md-9">
    <img src="./pages/background.jpg" alt="" width="100%">
</div>
</div>
</div>
