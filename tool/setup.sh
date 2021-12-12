BDIR=`dirname $0`
if [ "$BDIR" = "" ]
then 
  BDIR="."
fi

mkdir -p $BDIR/../../log/sam
chmod ugo+rwx $BDIR/../../log/sam

SDIR=$BDIR/../../sales_server_support

ln -sf ../sales_server_support $BDIR/../sales_server_support

mkdir -p $SDIR
chmod ugo+rwx $SDIR

YDIR=`date +%Y`
PREV_YEAR=`expr $YDIR - 1`
DIR_LIST="templates_c print_profile publish_doc FROM_MOBILE invoice quotation"
DIR_LIST="$DIR_LIST product_delivery_pdf Support_Upload_Files Support_Upload_Files/$YDIR Support_Upload_Files/$PREV_YEAR"
DIR_LIST="$DIR_LIST temp_pdf_generator proforma Feedback_Uploaded_Files"
DIR_LIST="$DIR_LIST AgreementCopy CC_REQUEST cust_quote Employee_Leave_Type"
DIR_LIST="$DIR_LIST onlinequote Photo receipt Release_Note Resume"
DIR_LIST="$DIR_LIST presales_activity"
DIR_LIST="$DIR_LIST Collection support_report_EE"
DIR_LIST="$DIR_LIST receipt_reported netcore_response"
DIR_LIST="$DIR_LIST test"
DIR_LIST="$DIR_LIST image"

for i in `echo $DIR_LIST`
do
  mkdir -p $SDIR/$i
  chmod ugo+rwx $SDIR/$i
done

SP_DIR_LIST="Feedback_Uploaded_Files"
for i in `echo $SP_DIR_LIST`
do
  chmod -R ugo+rwx $SDIR/$i
done

### May need to run the script
#  sudo chown -R www-data.www-data ../../sales_server_support/Feedback_Uploaded_Files
##

