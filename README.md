Stolen Stars
======================

Stolen Stars was an online role playing assistance tool, which tracked the location of players, planets, ships and more within a game.

To run this container, map an port to 9090.  By default, data is stored in an SQLite database within the container, so it is lost when the container is deleted.  It is recommended you configure the system for MySQL, but at the moment this requires patching the source code to specify the MySQL configuration (though the MySQL database itself is fully supported).

An example of running this container:

```
docker run -p 9090:9090 redpointgames/stolen-stars
```

The default username is `admin` and the password is `test`.

This source code is MIT licensed.