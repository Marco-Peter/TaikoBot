import QtQuick
import QtQuick.Controls.Material
import QtQuick.Controls

// Main shell for authenticated users: side drawer + StackView
Page {
    id: shell

    header: ToolBar {
        RowLayout {
            anchors.fill: parent

            ToolButton {
                icon.name: "menu"
                text: "☰"
                onClicked: drawer.open()
            }

            Label {
                id: titleLabel
                text: navigator.currentTitle
                font.pixelSize: 18
                elide: Label.ElideRight
                Layout.fillWidth: true
            }

            // Karma badge
            Label {
                text: AuthStore.karma === null ? "∞" : ("⚡ " + AuthStore.karma)
                font.pixelSize: 14
                rightPadding: 12
                visible: AuthStore.authenticated
            }
        }
    }

    Drawer {
        id: drawer
        width: 220
        height: parent.height

        Column {
            anchors.fill: parent
            spacing: 0

            // User info header
            Rectangle {
                width: parent.width
                height: 80
                color: Material.primary
                Label {
                    anchors.centerIn: parent
                    text: AuthStore.firstName
                    color: "white"
                    font.pixelSize: 18
                }
            }

            ItemDelegate {
                width: parent.width
                text: "Dashboard"
                onClicked: { navigator.navigate("dashboard"); drawer.close() }
            }
            ItemDelegate {
                width: parent.width
                text: "Courses"
                visible: AuthStore.canEditCourses
                onClicked: { navigator.navigate("courses"); drawer.close() }
            }
            ItemDelegate {
                width: parent.width
                text: "Users"
                visible: AuthStore.canEditUsers
                onClicked: { navigator.navigate("users"); drawer.close() }
            }
            ItemDelegate {
                width: parent.width
                text: "Profile"
                onClicked: { navigator.navigate("profile"); drawer.close() }
            }

            Item { Layout.fillHeight: true }

            ItemDelegate {
                width: parent.width
                text: "Sign Out"
                onClicked: { AuthStore.logout(); drawer.close() }
            }
        }
    }

    AppNavigator {
        id: navigator
        anchors.fill: parent
    }
}
