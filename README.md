## About AtounAfrica

AtounAfrica is african marketplace for made in africa creators.We believe in african creator for they creativity and we want raise up together. AtounAfrica comes with Amazing features, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## AUTH

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## USER

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

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
    1: livraison
    2: expédié
    3: validé
    4: remboursé

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
     Les messages sont carcatérisés: 
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



## Interactions with Product



## Code of Conduct



## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to ADANDE Appolinaire via [adandappolinaire229@gmail.com](adandappolinaire229@gmail.com). All security vulnerabilities will be promptly addressed.


## License

The Atoun Africa Marketplace is  licensed under the [AtounAfrica]().
