import QtQuick
import QtQuick.Controls.Material

// Elevated card container
Rectangle {
    default property alias content: contentItem.data

    radius: 8
    color: Material.backgroundColor
    layer.enabled: true
    layer.effect: null  // shadow via elevation

    // Minimal elevation shadow
    Rectangle {
        anchors { fill: parent; margins: -1 }
        radius: parent.radius + 1
        color: "transparent"
        border.color: Qt.rgba(0, 0, 0, 0.08)
        border.width: 1
        z: -1
    }

    Item {
        id: contentItem
        anchors { fill: parent; margins: 12 }
    }
}
