    docker build -t skosmos .
    docker run -d -p 8872:80 -v /var/docker/virtuoso/silknow/Skosmos/config:/config --name silknow_skosmos skosmos

    <!--
    docker run -i -t -p 8000:80 -v /Users/pasquale/git/Skosmosp/config:/config --name silknow_skosmos skosmos
     -->

    docker stop silknow_skosmos
    docker rm silknow_skosmos
    docker rmi skosmos
