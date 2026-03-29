function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(
        function () {
            console.log("Text copied to clipboard");
        },
        function (err) {
            console.error("Could not copy text: ", err);
        },
    );
}

document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById("copyButton");
    if (button) {
        button.addEventListener("click", function () {
            const textToCopy = "This is the text to copy!";
            copyToClipboard(textToCopy);
        });
    }
});
