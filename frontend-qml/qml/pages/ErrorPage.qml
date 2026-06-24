import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    property string message: "Something went wrong."

    ColumnLayout {
        anchors.centerIn: parent
        spacing: 16

        Label {
            text: "⚠"
            font.pixelSize: 48
            Layout.alignment: Qt.AlignHCenter
        }

        Label {
            text: message
            wrapMode: Text.Wrap
            horizontalAlignment: Text.AlignHCenter
            Layout.fillWidth: true
            Layout.leftMargin: 32
            Layout.rightMargin: 32
        }

        TButton {
            text: "Go back"
            Layout.alignment: Qt.AlignHCenter
            onClicked: StackView.view.pop()
        }
    }
}
