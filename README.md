## About AtounAfrica

AtounAfrica is african marketplace for made in africa creators.We believe in african creator for they creativity and we want raise up together. AtounAfrica comes with Amazing features, such as:


## AUTH



## USER

When user request to restore some account, notification should be sended for all admins



## CREATOR


## PRODUCT
creator can add product disponibility
    -1: in rupture
     0: disponible by comand
     1: disponible
     let's specify also number of product if only if product disponiblity is 1




## CATEGORY




## ORDER
order status is defined as tinyInteger
   -1: annulé 
    0: paiement
    1: delievering in attempt
    2: delievering
    3: expédié
    4: validé
    5: annuler

    An order can contain multiple products but for the same creator. 
    When products are for the différent creator, we should create two differents orders


    Order_item: it's have one to many relationship with orders. 
    Order_item represents one product in orders
    His Status is different to orders satatus and may be:
     -1 : remboursé
     0 : en cours
     1 : payé



     For Payments
          amount_paid est le montant effectivement payé jusqu'à présent.
          payment_type indique le type de paiement (par tranches ou en un coup).
          payment_status représente l'état du paiement (remboursé, en cours, payé).

          status: -1(remboursé) ,  0(en cours), 1 (payé)
          type: 0(par tranches), 1 (en un coup)

          5.0000 Fcfa is the contributions minimum

     Une transaction ne peut être mise à jour. On ne peut changer le montant payé manuellment. On ne peut changer la commande pour laquelle la transaction est effectuée.

     On peut mettre à jour la commande à laquelle appartient un order_item si et seulement si la commande est encore au statut -1. Mais si une première transaction a été déjà effectuée à propos deladite commande, celà reste impossible.


     Pour se faire rembourser une commande, l'utilisateur envoie une requête aux administrateurs. (notifications, et mail envoyé)


## Message
     Les messages sont caractérisés par: 
     content => pour stocker soit du texte soit l'url de l'image
     type => image ou text
     receiver_type => user, vendor, admin

     Mise à jour de message
          content
          To update message, user shouldn't specify type, receiuver_type, receiver_id,.

          In sum, only the content (text) can be updated. When the content type is image, nothing.
     
     Si un message est vu, il ne pourrait pas être modifié
     
     Delete Message
          Only message sender can delete message: soft delete

     Gestion des messages relatifs aux admins
          Si le receiver_id n'est pas précisé ou est 0, le message est destiné à AtounAfrica



## Interactions with Product

     getCommentsByProducts route is public route. We can't access to user_id by auth when it's connected. So, to verify if some comment is created by 
     this user, we should specify it's id in params as request.

     Same thing for like.



## Code of Conduct



## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to ADANDE Appolinaire via [adandappolinaire229@gmail.com](adandappolinaire229@gmail.com). All security vulnerabilities will be promptly addressed.


## License

The Atoun Africa Marketplace is  licensed under the [AtounAfrica]().
