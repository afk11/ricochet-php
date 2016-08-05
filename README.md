### Ricochet-IM for PHP

#### Requirements (for installation):

#### Requirements (for contributors):

If you're working on protobufs, you'll need the compiler available: 

https://github.com/drslump/Protobuf-PHP#installation

    sudo apt install protobuf-compiler
    pear channel-discover pear.pollinimini.net
    pear install drslump/Protobuf-beta

  protoc \
    --plugin=protoc-gen-php='/usr/bin/protoc-gen-php' \
    --proto_path='/home/user/git/ricochet-im/resource/' \
    --php_out=':src/Channel/Proto' \

protoc \
    --plugin=protoc-gen-php='/usr/bin/protoc-gen-php' \
    --proto_path=':resource/' \
    --proto_path=':vendor/rgooding/library/DrSlump/Protobuf/Compiler/protos' \
    --php_out=':src/' \
    '/home/user/git/ricochet-im/resource/protocol-controlchannel.proto'