import QtQuick
import QtQuick.Controls.Material
import QtQuick.Layouts
import "../components"

Page {
    id: root

    header: ToolBar {
        RowLayout {
            anchors.fill: parent
            anchors.leftMargin: 12
            anchors.rightMargin: 12

            Label {
                text: "Dashboard"
                font.pixelSize: 18
                font.bold: true
                Layout.fillWidth: true
            }

            TKarmaDisplay { karma: AuthStore.karma }
        }
    }

    TOfflineBanner {
        id: offlineBanner
        anchors { top: parent.top; left: parent.left; right: parent.right }
        pendingCount: SyncEngine.pendingCount
        visible: !SyncEngine.online
    }

    ScrollView {
        anchors {
            top: offlineBanner.visible ? offlineBanner.bottom : parent.top
            left: parent.left
            right: parent.right
            bottom: parent.bottom
        }
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 12

            // Payment warning
            Rectangle {
                visible: AuthStore.openPayment > 0
                Layout.fillWidth: true
                Layout.leftMargin: 12
                Layout.rightMargin: 12
                Layout.topMargin: 12
                height: paymentRow.implicitHeight + 16
                color: Material.color(Material.Orange, Material.Shade100)
                radius: 8

                RowLayout {
                    id: paymentRow
                    anchors { fill: parent; margins: 12 }

                    Label {
                        text: "Open payment: CHF " + AuthStore.openPayment
                        font.bold: true
                        color: Material.color(Material.Orange, Material.Shade900)
                        Layout.fillWidth: true
                    }
                }
            }

            // Greeting
            Label {
                text: "Hello, " + AuthStore.firstName + "!"
                font.pixelSize: 16
                Layout.leftMargin: 12
                Layout.topMargin: 8
            }

            // Section header
            Label {
                text: "Upcoming lessons"
                font.pixelSize: 14
                font.bold: true
                color: Material.accent
                Layout.leftMargin: 12
            }

            // Loading indicator
            BusyIndicator {
                visible: LessonStore.dashboardLoading
                Layout.alignment: Qt.AlignHCenter
            }

            // Lesson list
            Repeater {
                model: LessonStore.dashboardLessons

                TCard {
                    required property var modelData
                    required property int index
                    Layout.fillWidth: true
                    Layout.leftMargin: 12
                    Layout.rightMargin: 12
                    height: lessonCol.implicitHeight + 24

                    MouseArea {
                        anchors.fill: parent
                        onClicked: StackView.view.push("LessonShowPage.qml",
                                       { lessonId: modelData.id })
                    }

                    ColumnLayout {
                        id: lessonCol
                        anchors { fill: parent; margins: 12 }
                        spacing: 4

                        RowLayout {
                            Label {
                                text: modelData.title || modelData.course_name || "Lesson"
                                font.bold: true
                                Layout.fillWidth: true
                                elide: Text.ElideRight
                            }
                            TParticipationBadge {
                                participation: modelData.my_participation || ""
                                visible: (modelData.my_participation || "") !== ""
                            }
                        }

                        Label {
                            text: Qt.formatDateTime(
                                new Date(modelData.start),
                                "ddd dd.MM.yyyy HH:mm"
                            )
                            font.pixelSize: 12
                            opacity: 0.7
                        }
                    }
                }
            }

            // Empty state
            Label {
                visible: LessonStore.dashboardLessons.length === 0 && !LessonStore.dashboardLoading
                text: "No upcoming lessons"
                opacity: 0.5
                Layout.alignment: Qt.AlignHCenter
                Layout.topMargin: 32
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Component.onCompleted: LessonStore.loadDashboard()

    Connections {
        target: AuthStore
        function onSessionRestored() { LessonStore.loadDashboard() }
    }

    Connections {
        target: LessonStore
        function onParticipationChanged() { LessonStore.loadDashboard() }
    }
}
