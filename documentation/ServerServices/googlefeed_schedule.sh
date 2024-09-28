RESULT=$(systemctl is-active googlefeed_schedule.service)

mkdir -p "/home/googlefeed/logs"

if [ $RESULT !=  "active" ]; then

RES=$(systemctl status googlefeed_schedule.service)

echo "$RES" >> "/home/googlefeed/logs/errorlog.$(date +'%Y-%m-%d-%T').log"

systemctl start googlefeed_schedule.service

fi
