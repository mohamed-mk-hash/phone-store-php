$('.delete_client_btn').click(function (e) {
    e.preventDefault();
    var clientId = $(this).val();
    var button = $(this);
    var action = button.text().toLowerCase();

    swal({
        title: "Êtes-vous sûr?",
        text: `Voulez-vous vraiment ${action} ce client?`,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willProceed) => {
        if (willProceed) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'id': clientId,
                    'delete_client_btn': true,
                },
                success: function (response) {
                    if (response == 200) {
                        swal("Succès!", `Client ${action === "bloquer" ? "bloqué" : "débloqué"} avec succès.`, "success");
                        if (action === "bloquer") {
                            button.text("Débloquer").removeClass("btn-danger").addClass("btn-success");
                        } else {
                            button.text("Bloquer").removeClass("btn-success").addClass("btn-danger");
                        }
                    } else {
                        swal("Erreur!", "Quelque chose s'est mal passé.", "error");
                    }
                }
            });
        }
    });
});

$('.delete_shipping_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer ce lieu de livraison !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'shipping_id': id,
                    'delete_shipping_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Succès !", "Lieu de livraison supprimé avec succès", "success");
                        $("#shipping_table").load(location.href + " #shipping_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé !", "error");
                    }
                }
            });
        }
    });
});

$('.delete_reparation_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'service_id': id,
                    'delete_service_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Bravo !", "Service supprimé avec succès", "success");
                        $("#reparation_table").load(location.href + " #reparation_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});

$('.delete_hero_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'id': id,
                    'delete_hero_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Bravo !", "Héros supprimé avec succès", "success");
                        $("#hero_table").load(location.href + " #hero_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});

$('.delete_product_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'product_id': id,
                    'delete_product_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Bravo !", "Produit supprimé avec succès", "success");
                        $("#product_table").load(location.href + " #product_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});

$('.delete_category_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'category_id': id,
                    'delete_category_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Bravo !", "Catégorie supprimée avec succès", "success");
                        $("#category_table").load(location.href + " #category_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});

$('.delete_promo_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer cette publicité !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'promo_id': id,
                    'delete_promo_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Succès !", "Publicité supprimée avec succès", "success");
                        $("#promo_table").load(location.href + " #promo_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});

$('.delete_commentaire_btn').click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    swal({
        title: "Êtes-vous sûr ?",
        text: "Une fois supprimé, vous ne pourrez pas récupérer cette commentaire !",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            $.ajax({
                method: "POST",
                url: "code.php",
                data: {
                    'commentaire_id': id,
                    'delete_commentaire_btn': true,
                },
                success: function (response) {
                    console.log(response);
                    if (response == 200) {
                        swal("Succès !", "commentaire supprimée avec succès", "success");
                        $("#review_table").load(location.href + " #review_table");
                    } else if (response == 500) {
                        swal("Erreur !", "Quelque chose s'est mal passé", "error");
                    }
                }
            });
        }
    });
});