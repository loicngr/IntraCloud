if lsof -Pi :2729 -sTCP:LISTEN -t >/dev/null ; then
    echo "running"
else
    echo "not running"
    cd api && php bin/console run:websocket-server
fi