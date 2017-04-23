[![Latest Stable Version](https://poser.pugx.org/dtkahl/php-access-control/v/stable)](https://packagist.org/packages/dtkahl/php-access-control)
[![License](https://poser.pugx.org/dtkahl/php-access-control/license)](https://packagist.org/packages/dtkahl/php-access-control)
[![Build Status](https://travis-ci.org/dtkahl/php-access-control.svg?branch=master)](https://travis-ci.org/dtkahl/php-access-control)

# PHP access control

This package provides an access control (right management) system based on user, roles, rights and objects.


#### User

The main reason of this package is to prove whether the user has a specific right or not.
A user can have a right on different ways:

- through a global role
- through an object role

#### Roles

A role is a defined set of rights and can extend another existing role. Roles can be assigned on a global scope or on a specific object.


#### Objects

An user can have a specific role for a specific object. As example: The User "John" has the role "author" on the object "BlogPost".
A object can be any class that implements `ObjectInterface`. As example it could be the Eloquent model class `BlogPost`.


## Installation

Install with [Composer](http://getcomposer.org):
```
composer require dtkahl/php-array-tools
```


## Usage

#### create User(s)

This is not really a big deal. You just need a class that implements the `UserAccessInterface`. 
This requires you to implement one method:

- `getGlobalRoles` - Returns an array of role names (strings) you want your user to have. The way you store this information is completely up to you.


#### create Objects

This is a step you can skip if you only want to implement global rights. If you wan to have object roles and rights you have to implement the `ObjectInterface` in your objects class.
This requires three methods:

- `getObjectIdentifier` - Returns an identifier as string. This is used to find the right rights in the later defined roles
- `getUserRoles` - Returns an array of role names (string) that the given user (parameter of this method) have in relation to this object instance. The way you store this information is completely up to you.
- `getRelatedObjects` - Returns an array of related objects (which also have to implement the interface). This is used for inheritance. As example: The User is allowed to delete a BlogComment because the BlogComment is related to the BlogPost for which the user has the role "author"


#### define roles an rights

This could take place anywhere in your application but needs to be done before checking rights. The best place could be inside a dependency injection container.

**Defining a role**

```php
$role_member = new AccessRole(
    "member", // role name
    ["access"],  // global rights as array
    [
        "blog_post" => ["view", "create"] // rights related to a specific object as array (by object identifier)
    ],
);
```

**Extending a role**

```php
$role_admin = new AccessRole(
    "admin", // role name
    ["do_admin_stuff"],  // global rights as array
    [],
    $role_member // extend the member role so you dont have to specify all rights a second time
);
```

**define an object**

```php
$object_blog = new AccessObject(
    "blog", // identifier of an object
    [$role_author, $role_subscriber] // array of object related roles
);
```

**create the judge instance**

```php
$judge = new Judge(
    [$role_member], // array of all defined global roles
    [$object_blog, $object_comment], // array of all defined objects
    $user // optional, default user to check rights for
);
```

## The Judge class

This is the main class to check rights or roles. You normally want let your dependency container to return a instance of this class.
It has the following public methods:

#### `registerRole($role)`

Register a new global role for the Judge instance.

#### `registerObject($object)`

Register a new object for the Judge instance.

#### `setUser($user)`

Set the default user for the Judge instance.

#### `checkRight($rights, $object = null, $user = null)`

Throws `NotAllowedException` if the user do not have the given right(s).
If given object is null it only checks global rights.
If given user is null it uses the default user.

**Example:**
```php
$comment = BlogComment::find('1');
$judge->checkRight('edit', $comment); // check if the default user is allowed to edit a specific comment
```

#### `hasRight($rights, $object = null, $user = null)`

This is a proxy for `checkRights()` but instead of throwing an exception it only return true or false.

#### `checkRole`

Throws `NotALlowedException` if the user do not have the given role.
If given object is null it only checks global roles.
If given user is null it uses the default user.

**Example:**
```php
$comment = BlogComment::find('1');
$judge->checkRole('creator', $comment); // check if the default user is the creator of this comment
```

#### `hasRight`

This is a proxy for `checkRole()` but instead of throwing an exception it only return true or false.