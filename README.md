    docker build -t skosmos .
    docker run -i -t -p 8000:80 -v /Users/pasquale/Desktop/docker-skosmos/config.ttl:/var/www/html/config.ttl --name silknow_skosmos skosmos
    docker run -d -p 8872:80 --name silknow_skosmos skosmos
