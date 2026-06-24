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
            anchors.rightMargin: 8

            Label {
                text: "Courses"
                font.pixelSize: 18
                font.bold: true
                Layout.fillWidth: true
            }

            ToolButton {
                visible: AuthStore.canEditCourses
                text: "+"
                font.pixelSize: 22
                onClicked: StackView.view.push("CourseEditPage.qml", { courseId: 0 })
            }
        }
    }

    ScrollView {
        anchors.fill: parent
        contentWidth: availableWidth

        ColumnLayout {
            width: parent.width
            spacing: 8

            Item { Layout.preferredHeight: 8 }

            // Loading
            BusyIndicator {
                visible: CourseStore.loading && CourseStore.courses.length === 0
                Layout.alignment: Qt.AlignHCenter
            }

            Repeater {
                model: CourseStore.courses

                TCard {
                    required property var modelData
                    required property int index
                    Layout.fillWidth: true
                    Layout.leftMargin: 12
                    Layout.rightMargin: 12
                    height: courseRow.implicitHeight + 24

                    MouseArea {
                        anchors.fill: parent
                        onClicked: StackView.view.push("CourseShowPage.qml",
                                       { courseId: modelData.id })
                    }

                    ColumnLayout {
                        id: courseRow
                        anchors { fill: parent; margins: 12 }
                        spacing: 4

                        Label {
                            text: modelData.name
                            font.bold: true
                            font.pixelSize: 15
                            Layout.fillWidth: true
                            elide: Text.ElideRight
                        }

                        RowLayout {
                            Label {
                                text: modelData.description || ""
                                font.pixelSize: 12
                                opacity: 0.7
                                Layout.fillWidth: true
                                elide: Text.ElideRight
                            }

                            Label {
                                text: (modelData.participants_count || 0) + " / " + (modelData.capacity || "?")
                                font.pixelSize: 12
                                opacity: 0.7
                            }
                        }
                    }
                }
            }

            Label {
                visible: CourseStore.courses.length === 0 && !CourseStore.loading
                text: "No courses found"
                opacity: 0.5
                Layout.alignment: Qt.AlignHCenter
                Layout.topMargin: 32
            }

            Item { Layout.preferredHeight: 24 }
        }
    }

    Component.onCompleted: CourseStore.loadCourses()
}
