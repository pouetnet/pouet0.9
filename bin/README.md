# Maintainers tools

## Can I use them ?

You guys can't use most of those tools, as you'll need access to the live
server to use most of those.

## What does what ?

- `backup` *(server-side)* is the server-side backup script run nightly on the
    server. Scene.org does some other backups on her side too.
- `cleanup_codebase` *(client-side)* can be used by anyone, it's used to apply
    some dumb rules on the codebase to ease collaboration. e.g. enforce the use
    of 4 softtabs to indent the code. It should be run from time to time by the
    maintainers to make sure that the codebase is clean. New rules can be easily
    added.
- `deploy` *(server-side)* is the server-side script that deploys a new version
    when Github tells Pouët that there is one.
- `dump_DDL` *(client-side)* is used to dump the live schema of the database in
    the local `pouet.sql` file. Then the commiter can commit the change and keep
    track of the changes that happened to the database schema.
- `dump_SQL_for_dev` *(client-side)* can be launched to generate an updated
    version of the free to use data dumb that is offered to anyone willing to
    work on a local version of pouet. The output goes in
    `contribs/pouet_with_sample_data.sql`
- `sync` *(client-side)* can sync the current code base on the staging and live
    servers, and the other way. You `sync push live`, `sync pull live`,
    `sync push live` and `sync push live`. Pretty explicit no ?
- `sync_v2` *(client-side)* is the same as `sync`, but dedicated to Pouët v2,
    which is still being worked on.
