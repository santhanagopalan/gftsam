host_name_val=`hostname`

PATH=/usr/local/bin:$PATH

if [ $# -ne 3 ]
then
  echo "Invalid usage"
  exit 1
fi

emp_id=$1
for_app=$2
notification_id=$3
D=`dirname $0`
cd $D
php send_push_notification_now.php $emp_id $for_app $notification_id


