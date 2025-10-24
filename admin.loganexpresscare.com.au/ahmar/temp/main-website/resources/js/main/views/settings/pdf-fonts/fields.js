import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "pdf-fonts?fields=id,xid,name,file,file_url,user_kashida,use_otl";
    const addEditUrl = "pdf-fonts";
    const { t } = useI18n();

    const initData = {
        name: "",
        file: undefined,
        user_kashida: 0,
        use_otl: 255
    };

    const columns = [
        {
            title: t("pdf_font.name"),
            dataIndex: "name",
        },
        {
            title: t("pdf_font.user_kashida"),
            dataIndex: "user_kashida",
        },
        {
            title: t("pdf_font.use_otl"),
            dataIndex: "use_otl",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("pdf_font.name"),
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
