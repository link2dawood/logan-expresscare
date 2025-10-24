import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "holidays?fields=id,xid,name,year,date,month";
    const addEditUrl = "holidays";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
        year: "",
        month: "",
        date: undefined,
    };

    const columns = [
        {
            title: t("holiday.name"),
            dataIndex: "name",
        },

        {
            title: t("holiday.date"),
            dataIndex: "date",
        },
    ];

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
    };
};

export default fields;
