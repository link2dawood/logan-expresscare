import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "awards?fields=id,xid,name,description,award_price,active";
    const addEditUrl = "awards";
    const { t } = useI18n();

    const initData = {
        name: "",
        description: "",
        award_price: "",
        active: 1,
    };

    const columns = ref([
        {
            title: t("award.name"),
            dataIndex: "name",
        },
        {
            title: t("award.description"),
            dataIndex: "description",
        },
        {
            title: t("award.award_price"),
            dataIndex: "award_price",
        },
        {
            title: t("award.active"),
            dataIndex: "active",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
