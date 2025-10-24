import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "self/increments-promotions";
    const { t } = useI18n();
    const hashableColumns = [
        "current_designation_id",
        "promoted_designation_id",
        "user_id",
    ];

    const initData = {
        user_id: undefined,
        current_designation_id: undefined,
        promoted_designation_id: undefined,
        date: undefined,
        type: "increment",
        description: "",
        net_salary: "",
    };

    const columns = ref([
        {
            title: t("increment_promotion.date"),
            dataIndex: "date",
        },
        {
            title: t("increment_promotion.type"),
            dataIndex: "type",
        },
        {
            title: t("increment_promotion.current_designation_id"),
            dataIndex: "current_designation_id",
        },
        {
            title: t("increment_promotion.promoted_designation_id"),
            dataIndex: "promoted_designation_id",
        },
        {
            title: t("increment_promotion.net_salary"),
            dataIndex: "net_salary",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
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
    };
};

export default fields;
