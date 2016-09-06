### WARNING

This codebase is a work in progress, which doesn't even work correctly. 

 * I'm publishing to Github in case someone else can fix the issue (described later).
 
 * Besides one feature not working, the code that I assume to be working may, infact, be broken. 
 
The blocker for me right now is matching richochet's test vectors for the Cryptokey::sign256 function.
Amazingly, my implementation of signData() works just fine, but sign256 seems to fail when provided with short strings.

> No idea why, I've tried numerous things short of implementing the entire PKCS#2 signing procedure. This is not the solution we are looking for!

To test: 

 * set up tor using their debian repository
 * in your torrc expose the ControlPort, and create a password (`tor --hash-password testtesttesttest`)
 * take your ricochet client ID (without the protocol prefix) and update connect.php
 * generate a new RSA private key for your bot, and update connect.php
 * run connect.php - you'll connect, but fail auth due to an invalid sig. 

### Ricochet-IM for PHP

#### Requirements (installation for consumers):

No composer package yet, but tor >= 2.7.1 is required for ephemeral hidden services. 
 
This can be installed via the Tor projects debian repository, check their website for instructions.

#### Requirements (installation for contributors):

If you're working on protobufs, you'll need the compiler: 

https://github.com/drslump/Protobuf-PHP#installation

    sudo apt install protobuf-compiler
    pear channel-discover pear.pollinimini.net
    pear install drslump/Protobuf-beta

protoc \ 
    --plugin=protoc-gen-php='/usr/bin/protoc-gen-php' \
    --proto_path=':resource/' \
    --proto_path=':vendor/rgooding/library/DrSlump/Protobuf/Compiler/protos' \
    --php_out=':src/' \
    '/home/user/git/ricochet-im/resource/protocol-controlchannel.proto'

Or probably (it's been a while), run `build_proto`, modifying the script if you add new .proto files.