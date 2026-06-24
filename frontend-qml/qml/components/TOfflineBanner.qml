import QtQuick
import QtQuick.Controls.Material
import QtQuick.Controls

// Shown at the bottom of the screen when the app is offline
Rectangle {
    height: visible ? 40 : 0
    color: Material.color(Material.Orange, Material.Shade700)

    Behavior on height { NumberAnimation { duration: 200 } }

    Label {
        anchors.centerIn: parent
        text: SyncEngine.pendingOpsCount > 0
              ? ("Offline — " + SyncEngine.pendingOpsCount + " change(s) pending sync")
              : "Offline"
        color: "white"
        font.pixelSize: 13
    }
}
