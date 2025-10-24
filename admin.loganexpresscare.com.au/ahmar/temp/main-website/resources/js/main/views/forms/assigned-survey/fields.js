import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();

    const columns = [
        {
            title: t("assigned_surve.title"),
            dataIndex: "title",
        },
        {
            title: t("assigned_surve.start_date"),
            dataIndex: "start_date",
        },
        {
            title: t("assigned_surve.last_date"),
            dataIndex: "last_date",
        },
        {
            title: t("assigned_surve.submit_date"),
            dataIndex: "submit_date",
        },
        {
            title: t("assigned_surve.link"),
            dataIndex: "link",
        },
    ];

    return {
        columns,
    };
};

export default fields;
