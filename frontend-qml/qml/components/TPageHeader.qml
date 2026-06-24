import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts

RowLayout {
    property string title: ""
    property bool showBack: StackView.view && StackView.view.depth > 1

    ToolButton {
        text: "‹"
        font.pixelSize: 22
        visible: showBack
        onClicked: StackView.view.pop()
    }

    Label {
        text: title
        font.pixelSize: 20
        font.bold: true
        Layout.fillWidth: true
        elide: Text.ElideRight
    }
}
