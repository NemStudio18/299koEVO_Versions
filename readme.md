# 299Ko Versions

This repo is used to update automatically 299Ko from the configManager plugin.

## Create a new version

- Update changelog
- Change version in 299Ko/common/config.php
- Commit 299Ko repo with  Vx.x.x
- Create a tag in Git repo with vx.x.x tag name
- In file execUpdate :
    - Change $version to the version to create (x.x.x)
    - $commitLastVersion is the full SHA1 of the last version (eg 1.1.0)
    - $commitFutureVersion is the full SHA1 of the version to create (eg 1.2.0)
- In a terminal, exec `php execUpdate.php` in versions folder.
- In versions/core/versions.json, add the new version and modify last_version at top
- Add before & after run files
- Commit versions repo
