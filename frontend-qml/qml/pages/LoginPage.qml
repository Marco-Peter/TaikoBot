import QtQuick
import QtQuick.Controls.Material
import QtQuick.Controls
import QtQuick.Layouts

Page {
    title: "TaikoBot"

    Rectangle {
        anchors.fill: parent
        color: Material.backgroundColor

        ColumnLayout {
            anchors.centerIn: parent
            width: Math.min(parent.width - 32, 360)
            spacing: 16

            Image {
                Layout.alignment: Qt.AlignHCenter
                source: "qrc:/TaikoBot/assets/logo.png"
                width: 80; height: 80
                fillMode: Image.PreserveAspectFit
                visible: false  // enable when logo asset exists
            }

            Label {
                Layout.alignment: Qt.AlignHCenter
                text: "TaikoBot"
                font.pixelSize: 28
                font.bold: true
                color: Material.primary
            }

            Label {
                Layout.alignment: Qt.AlignHCenter
                text: errorLabel.text.length > 0 ? "" : "Sign in to your account"
                font.pixelSize: 14
                color: Material.hintTextColor
            }

            Label {
                id: errorLabel
                Layout.fillWidth: true
                wrapMode: Text.WordWrap
                color: Material.color(Material.Red)
                visible: text.length > 0
                horizontalAlignment: Text.AlignHCenter
            }

            TextField {
                id: emailField
                Layout.fillWidth: true
                placeholderText: "Email"
                inputMethodHints: Qt.ImhEmailCharactersOnly
                onAccepted: passwordField.forceActiveFocus()
                enabled: !AuthStore.loading
            }

            TextField {
                id: passwordField
                Layout.fillWidth: true
                placeholderText: "Password"
                echoMode: TextInput.Password
                onAccepted: doLogin()
                enabled: !AuthStore.loading
            }

            Button {
                Layout.fillWidth: true
                text: AuthStore.loading ? "Signing in…" : "Sign In"
                enabled: !AuthStore.loading && emailField.text.length > 0 && passwordField.text.length > 0
                onClicked: doLogin()
                highlighted: true
            }

            BusyIndicator {
                Layout.alignment: Qt.AlignHCenter
                running: AuthStore.loading
                visible: running
            }
        }
    }

    function doLogin() {
        errorLabel.text = ""
        AuthStore.login(emailField.text.trim(), passwordField.text)
    }

    Connections {
        target: AuthStore
        function onLoginFailed(error) {
            errorLabel.text = error || "Invalid credentials"
            passwordField.clear()
        }
    }
}
