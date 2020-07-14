TMP_DIR="../tmp"
APP_DIR="app"
API_DIR="api"

echo "-- Install ZIP --"
sudo apt install zip

echo "-- Install TREE --"
sudo apt install tree

if [ -d "$TMP_DIR" ]; then
  echo "-- The /tmp folder is already created. --"
  else
    echo "-- The /tmp is created. --"
    mkdir ../tmp
fi

echo "-- Create a VueJs Build --"
if [ -d "$APP_DIR" ]; then
  cd $APP_DIR && npm i -g @vue/cli && npm i && npm run build && cd ..
  else
    echo "-- The ./app folder is not created. --"
fi

echo "-- Install Symfony dependencies --"
if [ -d "$API_DIR" ]; then
  cd $API_DIR && composer install && npm i && cd ..
  else
    echo "-- The ./api folder is not created. --"
fi


echo "-- for setup email, you can install postfix --"
echo "-- OK --"
