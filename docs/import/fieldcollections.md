## Fieldcollections
Fieldcollections are something special here. Because they can (and will) have a 1:n relation, the connection between the Data and the Mapping is special.

![Interface](docs/images/fieldcollection.png)

As you can see in the screenshot above, we have to settings to make:

 - Field: Of course, the field from the Main Object
 - Keys: This is were the magic happens. Because fieldcollection may have a 1:n relation, we need to somehow map the Primary Key of the fieldcollection. This is done
  using a special CSV Syntax "FROM:TO,FROM:TO". The Interpreter will split the keys and search for the appropriate entry in the collection. If found, it will change the value,
  if its new, it will create a new entry. Because of the UI, you need to add this value to each entry of your fieldcollection mapping.

> I strongly recommend to use custom Interpreters to import Field-Collections as it might be a cleaner and better solution!
