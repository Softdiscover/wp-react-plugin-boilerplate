import {
    createRoot,
    render,
    StrictMode,
    createInterpolateElement,
} from "@wordpress/element";
import { Button, TextControl, Notice } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import { __ } from "@wordpress/i18n";
import "./scss/style.scss"; // Assuming you have your custom styling

const domElement = document.getElementById(
    window.myPluginTest.dom_element_id,
);
const PluginSettings = () => {
    const [optionData, setOptionData] = React.useState('');
    const [notice, setNotice] = React.useState(null);
    const [isLoading, setIsLoading] = React.useState(false);

    React.useEffect(() => {
        fetchOptionData();
    }, []);

    const fetchOptionData = () => {
        setIsLoading(true);
        apiFetch({ path: "/custom/v1/option", method: "GET" })
            .then(response => { setOptionData(response.data); setIsLoading(false); })
            .catch(() => { setNotice({ type: "error", message: __("Failed to fetch data", "custom-plugin") }); setIsLoading(false); });
    };

    const updateOption = () => {
        setIsLoading(true);
        apiFetch({ path: "/custom/v1/option", method: "PUT", data: { data: optionData }})
            .then(() => { setNotice({ type: "success", message: __("Option updated successfully", "custom-plugin") }); setIsLoading(false); })
            .catch(() => { setNotice({ type: "error", message: __("Failed to update option", "custom-plugin") }); setIsLoading(false); });
    };

    return (
        <>
            {notice && (
                <Notice status={notice.type} onRemove={() => setNotice(null)}>
                    {notice.message}
                </Notice>
            )}

            <div className="plugin-settings">
                <h1>{__("Plugin Settings", "my-plugin")}</h1>

                <TextControl
                    label={__("Example Option", "my-plugin")}
                    value={optionData}
                    onChange={setOptionData}
                />

                <Button variant="primary" onClick={updateOption}  disabled={isLoading} >
                    {__("Save Settings", "my-plugin")}
                </Button>
            </div>
        </>
    );
};

if (createRoot) {
    createRoot(domElement).render(
        <StrictMode>
            <PluginSettings />
        </StrictMode>,
    );
} else {
    render(
        <StrictMode>
            <PluginSettings />
        </StrictMode>,
        domElement,
    );
}
