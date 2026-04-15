function sendMessage() {
    const input = document.getElementById("userInput");
    const chat = document.getElementById("chatbox");
    let msg = input.value.toLowerCase();

    if (!msg) return;

    let reply = "I’m here to help you.";

    if (msg.includes("price")) reply = "Laptop ₹300/kg, Mobile ₹250/kg, Battery ₹180/kg.";
    else if (msg.includes("pickup")) reply = "Pickup is scheduled after admin approval.";
    else if (msg.includes("how")) reply = "Submit items and track status from your dashboard.";

    chat.innerHTML += `<p><b>You:</b> ${input.value}</p>`;
    chat.innerHTML += `<p><b>EcoBot:</b> ${reply}</p>`;
    chat.scrollTop = chat.scrollHeight;

    input.value = "";
}
