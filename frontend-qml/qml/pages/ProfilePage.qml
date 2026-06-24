import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root

    header: ToolBar {
        Label {
            anchors { left: parent.left; leftMargin: 16; verticalCenter: parent.verticalCenter }
            text: "My Profile"
            font.pixelSize: 18
            font.bold: true
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 16

            // Summary card
            TCard {
                Layout.fillWidth: true
                Layout.margins: 12
                height: summaryCol.implicitHeight + 24

                ColumnLayout {
                    id: summaryCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 6

                    Label {
                        text: AuthStore.firstName
                        font.pixelSize: 18
                        font.bold: true
                    }

                    Label {
                        text: AuthStore.role
                        font.pixelSize: 12
                        opacity: 0.6
                        font.capitalization: Font.Capitalize
                    }

                    RowLayout {
                        Label { text: "Karma:"; opacity: 0.7 }
                        TKarmaDisplay { karma: AuthStore.karma }
                    }

                    RowLayout {
                        visible: AuthStore.openPayment > 0
                        Label { text: "Open payment:"; opacity: 0.7 }
                        Label {
                            text: "CHF " + AuthStore.openPayment
                            font.bold: true
                            color: Material.color(Material.Orange, Material.Shade700)
                        }
                    }
                }
            }

            // Notification settings
            TCard {
                Layout.fillWidth: true
                Layout.leftMargin: 12
                Layout.rightMargin: 12
                height: notifCol.implicitHeight + 24

                ColumnLayout {
                    id: notifCol
                    anchors { fill: parent; margins: 12 }
                    spacing: 10

                    Label { text: "Lesson reminder"; font.bold: true }

                    RowLayout {
                        Label { text: "Notify"; Layout.fillWidth: true }
                        SpinBox {
                            id: reminderHours
                            from: 0; to: 72
                            value: currentSettings.lesson_notification_time || 24
                            textFromValue: function(v) { return v + " h before" }
                        }
                    }

                    TButton {
                        text: "Save settings"
                        enabled: !UserStore.loading
                        onClicked: saveSettings()
                    }

                    Label {
                        visible: UserStore.error.length > 0
                        text: UserStore.error
                        color: Material.color(Material.Red)
                        wrapMode: Text.Wrap
                        Layout.fillWidth: true
                    }
                }
            }

            // Logout
            TButton {
                text: "Log out"
                destructive: true
                Layout.alignment: Qt.AlignHCenter
                onClicked: logoutDialog.open()
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Dialog {
        id: logoutDialog
        title: "Log out?"
        modal: true
        anchors.centerIn: Overlay.overlay
        standardButtons: Dialog.Ok | Dialog.Cancel
        onAccepted: AuthStore.logout()
    }

    property var currentSettings: ({})

    Component.onCompleted: {
        // Settings come from the auth user object — not yet loaded here.
        // Extend AuthStore to expose settings if needed.
    }

    Connections {
        target: UserStore
        function onSettingsSaved() {
            saved.visible = true
            savedTimer.start()
        }
    }

    Label {
        id: saved
        text: "Saved!"
        color: Material.color(Material.Green)
        anchors { bottom: parent.bottom; horizontalCenter: parent.horizontalCenter; bottomMargin: 16 }
        visible: false
        font.bold: true

        Timer { id: savedTimer; interval: 2000; onTriggered: saved.visible = false }
    }

    function saveSettings() {
        UserStore.updateSettings({
            lesson_notification_time: reminderHours.value
        })
    }
}
