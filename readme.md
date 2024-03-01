
# Rendu Projet securité web
le but du projet est de créer un CRUD sur un objet de notre choix et de sécuriser le site .

## Injection sql

afin de contrer les possible injection sql , nous allors utiliser les requetes préparer de Symfony .


## Attaque XSS

Pour la sécurité de notre site nous allors devoir empecher les attaques xss . Pour l'empecher nous allons utiliser l'outil htmlspecialcharts 

```
 public function setName(string $Name): static
    {
        $this->Name = htmlspecialchars($Name);

        return $this;
    }
```
Cependant grace au filtre ( | raw ) nous pouvons empecher ce type d'attaque . Avec le text suivant <strong>Hello, world!</strong> voici le résultats sans et avec le filtre . 

    - &lt;strong&gt;Hello, world!&lt;/strong&gt; ( sans )

    - <strong>Hello, world!</strong> ( avec )


## attaque CSRF
Afin de contrer ces attaque nous allons utiliser un CSRF tokken .  Voici comment le mettre en place .
    dans un premier temps nous allons creer un token avec un certain format . le format ici est delete + jeu.id

    
    <form action="{{ path('app_delete_jeux', {'id': jeux.id}) }}l" method="POST">
		<input type="hidden" name="csrf" value="{{ csrf_token('delete' ~ jeux.id )}}">
		<input type="submit" value="Delete">
	</form>
 ce qui nous donne dans l'inspecteur d'element :
 ```
 <input type="hidden" name="csrf" value="001d57ccd6.6YcbljvdJzuOJsszLWw-pcF8IjvK1Mc8DFhugu5Sk9k.kch_106VQWHDF6xJHwdcxLsyUEiopaxfXjYisp4Z_baP03OkQp5CUPxRuA">
 ```
 ainsi si une personne modifie une des données , l'attribut value aura sa valeur changé . Ce qui posseras probleme lorsque l'on changeras de page car le code suivant existe : 
 ```
 if ($this->isCsrfTokenValid('delete' . $game->getId(), $r->request->get('csrf'))) {

            $em->remove($game);
            $em->flush();
        }

```
ce code verifie que le token ennvoyé par la requete est la même que celle généré . Ainsi si les deux sont différent alors le processus est stoppé

## autre sécurité 
voici une liste d'autre élément qui sécurisent notre site .

dans un premier temps le fichier securite.yaml qui défini quel role a acces a quel page 
```
 access_control:
        - { path: ^/jeux/add, roles: ROLE_ADMIN }
        - { path: ^/jeux/update, roles: ROLE_ADMIN }
        - { path: ^/jeux/delete, roles: ROLE_ADMIN }
        - { path: ^/jeux , roles : PUBLIC_ACCESS }
        - { path: ^/login , roles : PUBLIC_ACCESS }
        - { path: ^/register , roles : PUBLIC_ACCESS }

```

ensuite notre programme verifie le rôle de l'utilisateur pour les routes important grace a ces deux ligne . la premier nous permet de récupérer le role de l'utilisateur et la deuxieme est une condition a ajouter afin de verifié que l'on a bien un admin 
```
$roleUser = $this->getUser()->getRoles();

(&& in_array('ROLE_ADMIN', $roleUser)

```
notez que cette partie est peu utile si il n'existe pas de fonction permettant de changer de role 





    


    




## Authors

- [@ImDimeh](https://github.com/ImDimeh)


