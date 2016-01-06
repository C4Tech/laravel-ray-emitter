# Ray Emitter

Event Sourcing for Laravel. Made with love by C4 Tech and Design.

[![Latest Stable Version](https://poser.pugx.org/c4tech/ray-emitter/v/stable)](https://packagist.org/packages/c4tech/ray-emitter)
[![Build Status](https://travis-ci.org/C4Tech/laravel-ray-emitter.svg?branch=master)](https://travis-ci.org/C4Tech/laravel-ray-emitter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/C4Tech/laravel-ray-emitter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/C4Tech/laravel-ray-emitter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/C4Tech/laravel-ray-emitter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/C4Tech/laravel-ray-emitter/?branch=master)


## Installation and setup

1. Add `"c4tech/ray-emitter": "1.x"` to your composer requirements and run `composer update`.
2. Add `C4tech\RayEmitter\ServiceProvider` to `config/app.php` in the 'providers' array.
3. `php artisan vendor:publish`
4. Adjust `config/ray_emitter.php` to adjust your Event and Command handler prefices.


## Domain Driven Design

The bulk of Ray Emitter is a set of base classes to implement the command side of CQRS and domain driven design in your application.

### Commands

Commands encapsulate application state change requests at the Aggregate level.
In general, Commands serve as the internal API, displacing the Controller
layer of MVC architecture. Outward-facing APIs can parallel the Command
structure or map them. The key to successful implementation of Commands is
that they target one and only one Aggregate.

### Repository

Repositories serve as a outer layer to Aggregates, supporting a single
Aggregate by providing a way to rebuild the data for an Aggregate from the
Event Store.

### Aggregates

Aggregates encapsulate functionality and data related to a single Entity,
known as the Aggregate Root. In this implementation, Aggregates handle
Commands, providing validation of business logic, and transform successful
Commands into Events. Aggregates also handle the replaying of Events when
being rebuilt by the Repository.

### Events

Events are, in reality, nothing more than recorded changes caused by Commands.
However, they produce no side-effects in and of themselves. When replayed,
Events should only change data within the bounded context of the Aggregate.

### Entities

Entities are one of the primary building blocks of a Domain, serving as the
persisted representation of something that has an identity. In this
implementation, Entities are read-only representations of an application state
and contain all related Value Objects as properties. Since Aggregates should
only persist references to other Aggregate Roots, the Entity provides a way to
access the application state of another Aggregate without transgressing the
bounded context. Entities displace the Model layer of MVC architecture.

### Aggregate Roots

Aggregates Roots serve as the single primary Entity for an Aggregate. In this
implementation, Aggregate Roots are the write-enabled representation of an
Entity.

### Value Objects

Value Objects are the other primary build blocks of a Domain, serving as the
stateless representation of a value. One marked difference between a Value
Object and a simple property on a model is that Value Objects may contain
business logic for validation (e.g. an Email value object can validate that
the data provided to it fits the form of an email address). Value Objects do
not have a persistent thread of identity (e.g. every $20.00 bill has the same
value and are interchangeable as value objects). In this implementation, Value
Objects are properties on an Entity/AggregateRoot and are set/updated via
Events applied within an Aggregate.


## How Does This Interact with Laravel?

While the Domain is application-agnostic, this implementation has a few hooks into Laravel:

1. Its configuration system. While this isn't a strong dependency, the configuration is used to generate expected method names on the Aggregate for Command and Event handlers.
2. Its Database package. The Event Store extends the Eloquent Model class, and provides a migration file. Again, this is not a strong dependency.
3. Its Collection class. The Event Store and the Aggregate interact with the EventCollection, which extends the Laravel Collection class. Again, it's not a strong dependency.
4. Its Event package. The Event Store fires the Domain Event as a system Event on save to allow the application to produce side effects (e.g. send emails). Like the others, this is a weak dependency.
5. The Facade structure. The Event Store is accessed internally via Laravel's Facade which is registered using a Service Provider.

In the future, these dependencies may be adjusted to be more framework-agnostic.


### Getting the Most out of Laravel

Since there's not a strong dependency on Laravel, why is this a Laravel
package? That's easy: Laravel is expected to be used for the Query/ReadModel
side of the design. When the Domain Event is fired as a Laravel system event,
you should provide a Transformer to update the stored application state
snapshot. What's that? The "normal" Laravel Model structure. That means you
should still be maintaining database migrations and Eloquent Model classes.
However, these react to the broadcast Events rather than the Controllers
directly. Additionally, performing other system events (such as sending
emails) should be triggered by the Domain Events.


## What About Queries and Read Models?

You can construct how to handle Queries and Read Models. We suggest using the standard Laravel structure (Controllers, Models, etc) to perform read queries.


## Exceptions and Error Codes

This package throws Exceptions and provides HTTP-friendly error codes for the chance that the thrown exception is not caught. Below are the exceptions and their HTTP status codes, as well as a description of how to handle them.

1. 409 = Outdated Sequence. The Aggregate was updated before the Command was issued. Client should refresh read data and resubmit.

2. 422 = Sequence Mismatch. The issued Command expected the Aggregate to have a later sequence version than it does. Client should refresh read data and resubmit.

3. 501 = Event or Command Handler Missing. The Aggregate is missing the expected method for the Command or Domain Event.

4. 504 = UnknownProperty. The Entity/AggregateRoot does not have the expected property defined or the Aggregate Root cannot find a setter method for the property.
