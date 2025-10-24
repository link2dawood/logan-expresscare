import { ref } from "vue";
import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "attendances?fields=id,xid,user_id,x_user_id,duration,shift_duration,reason,workDuration{id,xid,attendance_id,x_attendance_id,start_time,end_time,duration,status},leave_type_id,x_leave_type_id,date,clock_in_time,clock_out_time,total_duration,user{id,xid,name,profile_image,profile_image_url,employee_number},workDuration{id,xid,status,duration},is_late,is_half_day,clock_in_date_time,clock_out_date_time,clock_in_ip_address,clock_out_ip_address,user:designation{id,xid,name},user:location{id,xid,name}";
    const addEditUrl = "attendances";
    const { t } = useI18n();
    const hashableColumns = ["user_id", "leave_type_id"];
    const { dayjs } = common();

    const initData = {
        user_id: undefined,
        leave_type_id: undefined,
        date: dayjs().utc().format("YYYY-MM-DD"),
        clock_in_time: undefined,
        clock_out_time: undefined,
        clock_in_ip_address: "",
        clock_out_ip_address: "",
        is_late: 0,
        is_half_day: 0,
        reason: "",
    };

    const columns = ref([
        {
            title: t("attendance.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("attendance.date"),
            dataIndex: "date",
        },
        {
            title: t("attendance.clock_in_date_time"),
            dataIndex: "clock_in_date_time",
        },
        {
            title: t("attendance.clock_out_date_time"),
            dataIndex: "clock_out_date_time",
        },
        {
            title: t("attendance.clock_in_ip_address"),
            dataIndex: "clock_in_ip_address",
        },
        {
            title: t("attendance.clock_out_ip_address"),
            dataIndex: "clock_out_ip_address",
        },
        {
            title: t("attendance.total_duration"),
            dataIndex: "total_duration",
        },
        {
            title: t("attendance.is_late"),
            dataIndex: "is_late",
        },
        {
            title: t("common.status"),
            dataIndex: "status",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        url,
    };
};

export default fields;
