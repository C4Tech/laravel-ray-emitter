# Ray Emitter

Event Sourcing for Laravel.


## Error Codes

1. 409 = Outdated Sequence. The Aggregate was updated before the Command was issued. Client should refresh read data and resubmit.

2. 422 = Sequence Mismatch. The issued Command expected the Aggregate to have a later sequence version than it does. Client should refresh read data and resubmit.

3. 501 = Event or Command Handler Missing. The Aggregate is missing the expected method for the Command or Domain Event.
