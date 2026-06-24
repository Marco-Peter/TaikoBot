import QtQuick
import QtQuick.Controls.Material

ApplicationWindow {
    id: root
    visible: true
    width: 400
    height: 800
    title: "TaikoBot"

    Material.theme: Material.Light
    Material.primary: Material.Teal
    Material.accent: Material.Orange

    // Show login or the main app navigator based on auth state
    Loader {
        id: mainLoader
        anchors.fill: parent
        source: AuthStore.authenticated ? "qrc:/TaikoBot/qml/TaikoBotApp.qml"
                                        : "qrc:/TaikoBot/qml/pages/LoginPage.qml"
    }

    // Offline banner overlays the whole screen
    TOfflineBanner {
        anchors.bottom: parent.bottom
        anchors.left: parent.left
        anchors.right: parent.right
        visible: SyncEngine.offline
    }

    Connections {
        target: AuthStore
        function onSessionExpired() {
            mainLoader.source = "qrc:/TaikoBot/qml/pages/LoginPage.qml"
        }
        function onAuthChanged() {
            // handled by Loader binding above
        }
    }
}
