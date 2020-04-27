const $button = document.querySelector("#btn")
const $inputCompanyName = document.querySelector("#companyName")
const $inputFirstName = document.querySelector("#firstName")
const $inputSurname = document.querySelector("#surname")
const $message = document.querySelector("#message")

const fetchCompany = async (formData) => {

    const url = "http://localhost:8080/search"
    const response = await fetch(url, { method: 'POST', body: formData })
    const data = await response.json()

    if (data.error) {
        $message.classList.remove("info");
        $message.classList.add("error");
        $message.innerHTML = data.message;
        return;
    }

    $message.classList.remove("error");
    $message.classList.add("info");
    $message.innerHTML = data.message;

}

$button.addEventListener("click", () => {

    const companyName = $inputCompanyName.value;
    const officerName = `${$inputFirstName.value}, ${$inputSurname.value}`;

    const formData = new FormData()
    formData.append('companyName', companyName)
    formData.append('officerName', officerName)

    fetchCompany(formData)
})