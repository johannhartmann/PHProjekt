[production]
; Language
language = "en"

; Paths
uploadPath = "/tmp/phprojekt_test/upload/"
tmpPath = "/tmp/phprojekt_test/tmp/"
applicationPath = "/tmp/phprojekt_test/application/"
webdavPath = "/tmp/phprojekt_test/webdav/"

; Database configuration
database.adapter = "Pdo_Mysql"
database.params.host = "localhost"
database.params.username = "test"
database.params.password = "test"
database.params.dbname = "phprojekt_test"
database.params.charset = "utf8"

; Logging
log.debug.filename = "/tmp/phprojekt_test/debug.log"
log.err.filename = "/tmp/phprojekt_test/err.log"
log.printStackTraces = false

; Application configuration
webdav.enabled = false
itemsPerPage = 3
userDisplayFormat = 0
searchStopwordList = ""
maxUploadSize = 512000
compressedDojo = true
useCacheForClasses = true
frontendMessages = true
validPeriod = 2
remindBefore = 15
pollingTime = 20
pollingLoop = 30
