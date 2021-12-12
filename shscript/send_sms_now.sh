host_name_val=`hostname`

PATH=/usr/local/bin:$PATH

if [ $# -ne 4 ]
then
  echo "Invalid usage"
  exit 1
fi

if [ $host_name_val = "samtest2" -o $host_name_val = "sam-store" -o $host_name_val = "sam-store-mbe" -o $host_name_val = "testsam1" -o $host_name_val = "samipl" ]
then
  sms_id=$1
  category=$2
  send_intl=$3
  sender_id=$4
  D=`dirname $0`
  cd $D
  php send_sms_now.php $sms_id $category $send_intl $sender_id
else
  echo " not supported for host: $host_name_val"
fi




