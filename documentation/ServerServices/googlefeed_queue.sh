RESULT=$(systemctl is-active googlefeed_queue.service)

mkdir -p "/home/googlefeed/logs"

if [ $RESULT !=  "active" ]; then

RES=$(systemctl status googlefeed_queue.service)

echo "$RES" >> "/home/googlefeed/logs/errorlog.$(date +'%Y-%m-%d-%T').log"

systemctl start googlefeed_queue.service

fi
